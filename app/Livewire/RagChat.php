<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Services\Ai\ChatInputSanitizer;
use App\Services\Ai\RagChatService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * @property-read bool $hasMessages
 */
#[Title('Lumion AI — Trade School Knowledge Base RAG Assistant')]
class RagChat extends Component
{
    public string $input = '';

    /**
     * Chat conversation messages stored in session state.
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
     *             content: string,
     *             distance: float,
     *             similarity: float,
     *             match_percentage: int
     *         }>,
     *         grounded: bool,
     *         latency_ms: float,
     *         model: string,
     *         system_prompt: string,
     *         min_threshold: float
     *     }|null
     * }>
     */
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
     * Submit a user message and execute the RAG retrieval + generation pipeline.
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
        ];

        // 1. Retrieval step
        $retrieval = $ragChatService->retrieveContext($userText);

        // 2. Strict grounding verification
        if (! $retrieval['grounded']) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "I'm sorry, but our trade school knowledge base does not currently contain verified information about that topic. Please contact our Admissions Office or Campus Student Services for personalized assistance!",
                'rag_details' => [
                    'retrieved_articles' => [],
                    'grounded' => false,
                    'latency_ms' => $retrieval['latency_ms'],
                    'model' => (string) config('ai.chat.model', 'gpt-4o-mini'),
                    'system_prompt' => 'Ungrounded query: No articles matched the similarity threshold ('.($retrieval['min_threshold'] * 100).'%). LLM call bypassed to enforce strict grounding.',
                    'min_threshold' => $retrieval['min_threshold'],
                ],
            ];

            return;
        }

        // 3. Prompt assembly
        $systemPrompt = $ragChatService->buildSystemPrompt($retrieval['articles']);
        $history = array_map(
            static fn (array $m): array => ['role' => $m['role'], 'content' => $m['content']],
            array_slice($this->messages, 0, -1)
        );
        $fullMessages = $ragChatService->buildMessages($systemPrompt, $history, $userText);

        // 4. Streamed generation
        $this->isStreaming = true;
        $accumulatedContent = '';

        foreach ($ragChatService->streamChatResponse($fullMessages) as $chunk) {
            $accumulatedContent .= $chunk;
            $this->stream(to: 'assistant-response', content: $chunk);
        }

        $this->isStreaming = false;

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $accumulatedContent,
            'rag_details' => [
                'retrieved_articles' => $retrieval['articles'],
                'grounded' => true,
                'latency_ms' => $retrieval['latency_ms'],
                'model' => (string) config('ai.chat.model', 'gpt-4o-mini'),
                'system_prompt' => $systemPrompt,
                'min_threshold' => $retrieval['min_threshold'],
            ],
        ];
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
            ->layout('layouts.app.header', ['title' => 'Lumion AI — Trade School RAG Assistant']);
    }
}
