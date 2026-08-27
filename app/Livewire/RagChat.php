<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\Ai\ChatInputSanitizer;
use App\Services\Ai\RagChatService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

/**
 * @property-read bool $hasMessages
 */
#[Title('RAG Evaluation Arena (Side-by-Side Benchmark)')]
class RagChat extends Component
{
    public string $input = '';

    /**
     * Chat conversation messages stored in session state.
     * Each turn consists of a user message followed by an assistant message
     * containing both RAG-grounded and raw LLM baseline responses.
     *
     * @var list<array{
     *     role: 'user'|'assistant',
     *     content: string,
     *     rag_details: array{
     *         retrieved_articles: list<array{
     *             id: int,
     *             title: string,
     *             slug: string,
     *             audience: string,
     *             summary: ?string,
     *             distance: float,
     *             similarity: float,
     *             match_percentage: int
     *         }>,
     *         grounded: bool,
     *         has_error: bool,
     *         latency_ms: float,
     *         model: string,
     *         system_prompt: string,
     *         min_threshold: float
     *     }|null,
     *     raw_details?: array{
     *         content: string,
     *         has_error: bool,
     *         latency_ms: float,
     *         model: string,
     *         system_prompt: string,
     *         grounded: bool
     *     }|null
     * }>
     */
    #[Locked]
    public array $messages = [];

    public bool $isStreaming = false;

    /**
     * Curated starter prompts demonstrating RAG capability across multiple trade domains.
     *
     * @var list<string>
     */
    public array $starterPrompts = [
        'What hyperbaric welding safety standards and pressure checks are required?',
        'How do prospective solar installation recruits apply for apprenticeship grants?',
        'What formulas and NEC tables should I study for the journeyman electrical exam?',
        'What emergency procedures and eye-wash protocols must workshop labs follow?',
    ];

    /**
     * Submit a user message and execute dual-pipeline generation (Grounded RAG vs Raw LLM).
     */
    public function sendMessage(ChatInputSanitizer $sanitizer, RagChatService $ragChatService): void
    {
        $sanitizedResult = $sanitizer->sanitize($this->input);

        if (! $sanitizedResult['is_valid']) {
            Flux::toast(
                text: $sanitizedResult['rejection_reason'] ?? __('Please enter a valid message.'),
                variant: 'danger'
            );

            return;
        }

        $userText = $sanitizedResult['safe_input'];

        // Rate limiting check: 20 messages per minute per IP
        $key = 'rag-chat:'.(request()->ip() ?? 'unknown');
        if (RateLimiter::tooManyAttempts($key, 20)) {
            $seconds = RateLimiter::availableIn($key);
            Flux::toast(
                text: __('Rate limit reached. Please wait :seconds seconds before asking another question.', ['seconds' => $seconds]),
                variant: 'danger'
            );

            return;
        }
        RateLimiter::hit($key, 60);

        // Reset input immediately
        $this->input = '';

        // Append user turn
        $this->messages[] = [
            'role' => 'user',
            'content' => $userText,
            'rag_details' => null,
            'raw_details' => null,
        ];

        // 1. Retrieval step for RAG
        $retrieval = $ragChatService->retrieveContext($userText);

        // Multi-turn conversational fallback: if ungrounded and prior conversation exists, retry with previous question context
        if (! $retrieval['grounded'] && count($this->messages) > 1) {
            $lastUserQuestion = $this->getLastUserQuestion();
            if ($lastUserQuestion !== null && $lastUserQuestion !== $userText) {
                $contextualQuery = "{$lastUserQuestion} — {$userText}";
                $retryRetrieval = $ragChatService->retrieveContext($contextualQuery);
                if ($retryRetrieval['grounded']) {
                    $retrieval = $retryRetrieval;
                }
            }
        }

        // Build separated conversation history for RAG and Raw pipelines to prevent context leakage
        $ragHistory = [];
        $rawHistory = [];
        for ($i = 0; $i < count($this->messages) - 1; $i++) {
            $msg = $this->messages[$i];
            if ($msg['role'] === 'user') {
                $ragHistory[] = ['role' => 'user', 'content' => $msg['content']];
                $rawHistory[] = ['role' => 'user', 'content' => $msg['content']];
            } elseif ($msg['role'] === 'assistant') {
                $ragHistory[] = ['role' => 'assistant', 'content' => $msg['content']];
                $rawHistory[] = ['role' => 'assistant', 'content' => $msg['raw_details']['content'] ?? $msg['content']];
            }
        }

        $chatModel = (string) config('ai.chat.model', 'gpt-4o-mini');
        $this->isStreaming = true;

        // 2. Grounded RAG Pipeline Generation
        $ragContent = '';
        $ragError = false;
        $systemPrompt = '';
        $displayArticles = [];

        if (! $retrieval['grounded']) {
            $ragContent = "I'm sorry, but our trade school knowledge base does not currently contain verified information about that topic. Please contact our Admissions Office or Campus Student Services for personalized assistance!";
            $systemPrompt = 'Ungrounded query: No articles matched the similarity threshold ('.($retrieval['min_threshold'] * 100).'%). LLM call bypassed to enforce strict grounding.';
        } else {
            $systemPrompt = $ragChatService->buildSystemPrompt($retrieval['articles']);
            $ragMessages = $ragChatService->buildMessages($systemPrompt, $ragHistory, $userText);

            try {
                foreach ($ragChatService->streamChatResponse($ragMessages) as $chunk) {
                    $ragContent .= $chunk;
                    $this->stream(to: 'rag-response', content: $chunk);
                }
            } catch (Throwable) {
                $ragError = true;
                $ragContent .= ($ragContent !== '' ? "\n\n" : '').'Unable to complete response from AI service. Please try again.';
                Flux::toast(text: __('Unable to complete RAG response. Please try again.'), variant: 'danger');
            }

            $displayArticles = array_map(
                static fn (array $a): array => [
                    'id' => $a['id'],
                    'title' => $a['title'],
                    'slug' => $a['slug'],
                    'audience' => $a['audience'],
                    'summary' => $a['summary'],
                    'distance' => $a['distance'],
                    'similarity' => $a['similarity'],
                    'match_percentage' => $a['match_percentage'],
                ],
                $retrieval['articles']
            );
        }

        // 3. Raw Baseline Pipeline Generation (Ungrounded Parametric Memory)
        $rawContent = '';
        $rawError = false;
        $rawSystemPrompt = $ragChatService->buildRawSystemPrompt();
        $rawMessages = $ragChatService->buildMessages($rawSystemPrompt, $rawHistory, $userText);
        $rawStartTime = hrtime(true);

        try {
            foreach ($ragChatService->streamChatResponse($rawMessages) as $chunk) {
                $rawContent .= $chunk;
                $this->stream(to: 'raw-response', content: $chunk);
            }
        } catch (Throwable) {
            $rawError = true;
            $rawContent .= ($rawContent !== '' ? "\n\n" : '').'Unable to complete baseline response from AI service.';
        } finally {
            $this->isStreaming = false;
        }

        $rawLatencyMs = round((hrtime(true) - $rawStartTime) / 1_000_000, 2);

        // Store combined turn with both perspectives
        $this->messages[] = [
            'role' => 'assistant',
            'content' => $ragContent,
            'rag_details' => [
                'retrieved_articles' => $displayArticles,
                'grounded' => $retrieval['grounded'] && ! $ragError,
                'has_error' => $ragError,
                'latency_ms' => $retrieval['latency_ms'],
                'model' => $chatModel,
                'system_prompt' => $systemPrompt,
                'min_threshold' => $retrieval['min_threshold'],
            ],
            'raw_details' => [
                'content' => $rawContent,
                'has_error' => $rawError,
                'latency_ms' => $rawLatencyMs,
                'model' => $chatModel,
                'system_prompt' => $rawSystemPrompt,
                'grounded' => false,
            ],
        ];
    }

    /**
     * Look up the previous user question from message history for contextual retry.
     */
    protected function getLastUserQuestion(): ?string
    {
        for ($i = count($this->messages) - 2; $i >= 0; $i--) {
            if ($this->messages[$i]['role'] === 'user') {
                return $this->messages[$i]['content'];
            }
        }

        return null;
    }

    /**
     * Populate and submit a pre-configured starter prompt.
     */
    public function loadSuggestion(string $prompt, ChatInputSanitizer $sanitizer, RagChatService $ragChatService): void
    {
        $this->input = $prompt;
        $this->sendMessage($sanitizer, $ragChatService);
    }

    /**
     * Clear the current session conversation history.
     */
    public function clearChat(): void
    {
        $this->messages = [];
        $this->input = '';
        Flux::toast(text: __('Conversation cleared.'), variant: 'info');
    }

    /**
     * Render the Livewire RAG chat view.
     */
    public function render(): View
    {
        return view('livewire.rag-chat')
            ->layout('layouts.app.header', ['title' => 'Trade School RAG Assistant']);
    }
}
