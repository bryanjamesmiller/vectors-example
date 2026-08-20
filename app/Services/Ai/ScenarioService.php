<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Enums\Audience;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

class ScenarioService
{
    /**
     * Curated pool of diverse trade school scenarios used as a robust fallback.
     *
     * @var list<array{title: string, audience: string, summary: string, content: string}>
     */
    protected array $fallbackScenarios = [
        [
            'title' => 'Underwater Welding Safety & Pressure Chamber Protocols',
            'audience' => 'students',
            'summary' => 'Critical hyperbaric welding safety standards, decompression protocols, and gas manifold checks for offshore technicians.',
            'content' => 'Hyperbaric and underwater welding requires strict adherence to ASME Section IX standards. Technicians must verify gas manifold pressure differentials before diving and monitor sealed electrode insulation. Never initiate an arc while oxygen saturation in the hyperbaric habitat exceeds safety thresholds.',
        ],
        [
            'title' => 'Journeyman Electrical Master Licensing Exam Tactics',
            'audience' => 'alumni',
            'summary' => 'Speed calculation tactics for branch circuit sizing, conduit fill tables, and National Electrical Code navigation.',
            'content' => 'Passing the journeyman electrical examination requires rapid mastery of NEC Chapter 9 tables. Practice timed conduit fill calculations and motor feeder overload protection formulas to maximize scoring on the practical code section.',
        ],
        [
            'title' => 'Solar Technician Apprenticeship Grants and Stipends',
            'audience' => 'recruits',
            'summary' => 'How prospective photovoltaic technicians can secure state clean energy grants and monthly tool allowances.',
            'content' => 'Clean energy vocational initiatives offer tuition assistance and tool stipends for solar photovoltaic installation recruits. Submit proof of enrollment and FAFSA documentation to receive full program coverage and safety equipment vouchers.',
        ],
        [
            'title' => 'Workshop Eye-Wash Stations & Chemical Splash Response',
            'audience' => 'teachers',
            'summary' => 'Inspection protocols and emergency response procedures for acid, solvent, and coolant exposure in workshop labs.',
            'content' => 'All vocational workshop labs must test eye-wash stations and emergency showers weekly. In the event of chemical contact, flush the affected area for a minimum of 15 minutes and immediately report the incident to campus safety personnel.',
        ],
        [
            'title' => 'CNC Multi-Axis Milling Speeds, Feeds & Toolpath Optimization',
            'audience' => 'students',
            'summary' => 'Feed rate formulas and carbide endmill toolpath strategies for machining titanium aerospace components.',
            'content' => 'High-efficiency milling in 5-axis CNC machining requires precise chip load calculations to prevent thermal deflection. Always verify helical entry parameters in Mastercam before initiating pocket roughing routines on titanium stock.',
        ],
        [
            'title' => 'Commercial Refrigeration Superheat & Subcooling Diagnostics',
            'audience' => 'teaching_assistants',
            'summary' => 'Troubleshooting low-temperature walk-in freezers using digital manifold gauges and psychrometric charts.',
            'content' => 'Diagnosing commercial HVAC-R walk-in freezers begins with calculating target superheat across the TXV sensing bulb. Compare liquid line subcooling against OEM condenser nameplate ratings to identify non-condensable contamination or refrigerant undercharge.',
        ],
        [
            'title' => 'FAA Part 147 Turbine Engine Borescope Inspection Standards',
            'audience' => 'students',
            'summary' => 'Optical inspection techniques for detecting thermal barrier coating loss on high-pressure turbine blades.',
            'content' => 'Aviation maintenance technicians must inspect CFM56 engine turbine rotor stages using articulating fiber-optic borescopes. Document all leading-edge micro-cracking against the aircraft engine maintenance manual allowable damage limits before approving airworthiness.',
        ],
        [
            'title' => 'Hydraulic Excavator Pilot Valve Pressure Troubleshooting',
            'audience' => 'recruits',
            'summary' => 'Diagnostic workflows for isolation valves and hydraulic pump displacement on heavy civil machinery.',
            'content' => 'When troubleshooting sluggish boom actuation on hydraulic excavators, connect 5,000 PSI gauges to the pilot manifold test ports. Verify main relief valve pressure cracking points prior to tearing down hydraulic swing motors.',
        ],
        [
            'title' => 'Plumbing Backflow Prevention Assembly Testing & Cross-Connection Control',
            'audience' => 'alumni',
            'summary' => 'Field testing reduced pressure principle (RPZ) backflow preventers for commercial potable water systems.',
            'content' => 'Annual certification of RPZ backflow preventers requires differential pressure gauge calibration across the check valve assemblies. Ensure relief valve discharge opens at a minimum of 2.0 PSID differential before signing municipal water compliance records.',
        ],
        [
            'title' => 'Electric Vehicle High-Voltage Battery Pack Isolation Safety',
            'audience' => 'teachers',
            'summary' => 'NFPA 70E electrical safety guidelines and Class 0 insulated glove testing for hybrid and EV automotive labs.',
            'content' => 'Before disassembling high-voltage automotive battery packs, technicians must verify zero potential using CAT III 1000V rated multimeters. Always perform daily dielectric air testing on 1000V insulating rubber gloves before handling orange high-voltage cabling.',
        ],
    ];

    /**
     * Generate a randomized trade school article scenario via OpenAI with minimal token usage.
     *
     * @return array{title: string, audience: string, summary: string, content: string}
     */
    public function generateRandomScenario(): array
    {
        $apiKey = config('openai.api_key');

        if (empty($apiKey)) {
            return $this->getRandomFallbackScenario();
        }

        try {
            $allowedAudiences = array_map(
                static fn (Audience $audience): string => $audience->value,
                Audience::cases()
            );

            $model = (string) config('ai.chat.model', 'gpt-4o-mini');

            $randomTopic = Arr::random([
                'Underwater Welding & Hyperbaric Safety',
                'Electrical Master Code & Conduit Sizing',
                'Solar Photovoltaic Grants & Incentives',
                'CNC 5-Axis Milling & Toolpath Optimization',
                'Commercial HVAC-R Superheat Diagnostics',
                'Aviation Turbine Borescope Inspection',
                'Heavy Equipment Hydraulic Troubleshooting',
                'Plumbing Backflow RPZ Assembly Testing',
                'Electric Vehicle High-Voltage Isolation Safety',
                'Structural Steel Flux-Cored Arc Welding (FCAW)',
                'Automotive CAN-Bus Oscilloscope Diagnostics',
                'Residential Heat Pump Inverter Board Diagnostics',
            ]);

            $response = OpenAI::chat()->create([
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You generate 1 realistic, randomized vocational trade school article scenario. Output JSON only: {"title":"...","audience":"students|recruits|alumni|teachers|teaching_assistants|administrators","summary":"...","content":"..."}. The content must be exactly 2-3 concise, technical sentences.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate a fresh vocational trade scenario focusing on: {$randomTopic}.",
                    ],
                ],
                'response_format' => ['type' => 'json_object'],
                'temperature' => 0.95,
                'max_completion_tokens' => 150,
            ]);

            $rawContent = $response->choices[0]->message->content ?? '{}';
            /** @var array<string, mixed>|null $data */
            $data = json_decode($rawContent, true);

            if (
                is_array($data) &&
                ! empty($data['title']) &&
                ! empty($data['content'])
            ) {
                $audience = is_string($data['audience'] ?? null) && in_array($data['audience'], $allowedAudiences, true)
                    ? $data['audience']
                    : 'students';

                return [
                    'title' => (string) $data['title'],
                    'audience' => $audience,
                    'summary' => (string) ($data['summary'] ?? ''),
                    'content' => (string) $data['content'],
                ];
            }
        } catch (Throwable) {
            // Gracefully fall back to rich curated pool on network or rate limit failure
        }

        return $this->getRandomFallbackScenario();
    }

    /**
     * Get a randomized scenario from the curated fallback pool.
     *
     * @return array{title: string, audience: string, summary: string, content: string}
     */
    public function getRandomFallbackScenario(): array
    {
        return Arr::random($this->fallbackScenarios);
    }
}
