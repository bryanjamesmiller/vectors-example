<?php

declare(strict_types=1);

namespace App\Services\Ai;

class ChatInputSanitizer
{
    public const int MAX_LENGTH = 500;

    /**
     * Common prompt injection and jailbreak signatures to detect and neutralize.
     *
     * @var list<string>
     */
    protected array $injectionPatterns = [
        '/\bignore\s+(all\s+)?(previous|prior|above)\s+instructions\b/i',
        '/\bdisregard\s+(all\s+)?(previous|prior|above)\s+instructions\b/i',
        '/\bforget\s+(all\s+)?(previous|prior)\s+instructions\b/i',
        '/\byou\s+are\s+now\s+(a|an)?\s*[a-z0-9_\-\s]+\b/i',
        '/\bact\s+as\s+(a|an)?\s*(unrestricted|jailbroken|dan|evil|hacked)\b/i',
        '/\b(system\s*prompt|system\s*override|role:\s*system|\[system\])\b/i',
        '/\bdo\s+anything\s+now\b/i',
        '/\bjailbreak\b/i',
    ];

    /**
     * Sanitize and validate user input for the RAG chat pipeline.
     *
     * @return array{
     *     is_valid: bool,
     *     safe_input: string,
     *     rejection_reason: ?string,
     *     was_modified: bool,
     *     flags: list<string>
     * }
     */
    public function sanitize(string $rawInput, int $maxLength = self::MAX_LENGTH): array
    {
        $flags = [];
        $wasModified = false;

        // 1. Remove null bytes and control characters (preserving tab \x09, newline \x0A, carriage return \x0D)
        $strippedControls = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $rawInput);
        if ($strippedControls !== $rawInput) {
            $flags[] = 'control_characters_removed';
            $wasModified = true;
        }

        // 2. Normalize unicode line breaks & excessive whitespace
        $normalized = (string) preg_replace('/[ \t]+/', ' ', $strippedControls);
        $normalized = (string) preg_replace('/(\r?\n){3,}/', "\n\n", $normalized);
        $trimmed = trim($normalized);

        // 3. Length validation
        if (mb_strlen($trimmed) > $maxLength) {
            return [
                'is_valid' => false,
                'safe_input' => '',
                'rejection_reason' => "Your message exceeds the maximum allowed length of {$maxLength} characters.",
                'was_modified' => true,
                'flags' => ['length_exceeded'],
            ];
        }

        // 4. HTML and script stripping
        $withoutTags = strip_tags($trimmed);
        if ($withoutTags !== $trimmed) {
            $flags[] = 'html_tags_stripped';
            $wasModified = true;
        }

        // Remove dangerous URI schemes and event-handler patterns (javascript:, data:, vbscript:)
        $cleanedText = (string) preg_replace('/\b(javascript|vbscript|data):/i', '$1_blocked:', $withoutTags);
        $cleanedText = (string) preg_replace('/\bon\w+\s*=/i', 'event_blocked=', $cleanedText);
        if ($cleanedText !== $withoutTags) {
            $flags[] = 'script_handlers_neutralized';
            $wasModified = true;
        }

        // 5. Prompt injection detection and neutralization
        $sanitizedContent = $cleanedText;
        foreach ($this->injectionPatterns as $pattern) {
            if (preg_match($pattern, $sanitizedContent)) {
                $flags[] = 'prompt_injection_pattern_detected';
                $wasModified = true;
                $sanitizedContent = (string) preg_replace($pattern, '[redacted-instruction]', $sanitizedContent);
            }
        }

        $finalInput = trim($sanitizedContent);

        // 6. Check for empty or purely non-alphanumeric/meaningless content
        if ($finalInput === '') {
            return [
                'is_valid' => false,
                'safe_input' => '',
                'rejection_reason' => 'Please enter a valid question or message.',
                'was_modified' => $wasModified,
                'flags' => $flags,
            ];
        }

        // Ensure there is at least one alphanumeric character
        if (! preg_match('/[\p{L}\p{N}]/u', $finalInput)) {
            return [
                'is_valid' => false,
                'safe_input' => '',
                'rejection_reason' => 'Please include words or numbers in your question.',
                'was_modified' => $wasModified,
                'flags' => array_merge($flags, ['no_alphanumeric_content']),
            ];
        }

        return [
            'is_valid' => true,
            'safe_input' => $finalInput,
            'rejection_reason' => null,
            'was_modified' => $wasModified,
            'flags' => $flags,
        ];
    }
}
