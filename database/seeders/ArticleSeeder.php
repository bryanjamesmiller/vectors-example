<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Audience;
use App\Models\Article;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define 4 distinct semantic vector clusters (512 dimensions each)
        $financialAidVector = $this->createClusterVector(0.08, 0.05);
        $safetyVector = $this->createClusterVector(-0.07, 0.09);
        $instructionVector = $this->createClusterVector(0.04, -0.08);
        $careerVector = $this->createClusterVector(-0.06, -0.06);

        $articles = [
            // Group 1: Financial Aid & Grants
            [
                'title' => 'Applying for Trade Tool Grants & Fee Waivers',
                'audience' => Audience::Students,
                'summary' => 'How trade school students can apply for state and private grants to cover expensive equipment, toolsets, and lab fees.',
                'content' => 'Trade school programs often require specialized equipment, such as welding hoods, multimeter testers, and power toolsets. This guide covers how to apply for institutional grant waivers and local foundation stipends to offset 100% of required tool kit expenses.',
                'embedding' => $this->perturbVector($financialAidVector, 0.01),
            ],
            [
                'title' => 'Veteran Financial Assistance & GI Bill Tuition Coverage',
                'audience' => Audience::Recruits,
                'summary' => 'Comprehensive breakdown of veteran education benefits for electrical and plumbing vocational certifications.',
                'content' => 'Military veterans entering trade school can leverage Post-9/11 GI Bill and VR&E programs to cover full tuition, housing allowances, and monthly tool stipends. Learn how to verify program eligibility with the campus VA certifying official.',
                'embedding' => $this->perturbVector($financialAidVector, 0.015),
            ],
            [
                'title' => 'Emergency Student Aid & Housing Relief Grants',
                'audience' => Audience::Students,
                'summary' => 'Access short-term emergency financial assistance for room, board, and unexpected trade school expenses.',
                'content' => 'When unexpected financial hardships arise, students can request emergency micro-grants for housing, food security, and transportation vouchers. Applications are reviewed confidentially within 48 hours.',
                'embedding' => $this->perturbVector($financialAidVector, 0.012),
            ],

            // Group 2: Workshop & Equipment Safety
            [
                'title' => 'Personal Protective Equipment (PPE) Guidelines for Welding Labs',
                'audience' => Audience::Students,
                'summary' => 'Mandatory safety gear, shade ratings, and fire-retardant clothing requirements for welding workshops.',
                'content' => 'Safety in the welding lab starts with proper personal protective equipment. Students must wear auto-darkening helmets with minimum shade 10-12, leather gauntlet gloves, flame-resistant jackets, and steel-toe boots at all times.',
                'embedding' => $this->perturbVector($safetyVector, 0.01),
            ],
            [
                'title' => 'High Voltage Electrical Lab Lockout/Tagout (LOTO) Procedures',
                'audience' => Audience::TeachingAssistants,
                'summary' => 'Standard operating procedures for de-energizing industrial circuits before student laboratory exercises.',
                'content' => 'Teaching assistants and lab technicians must ensure all three-phase electrical trainers are locked out and tagged out prior to student wiring exercises. Verify zero voltage with a calibrated CAT III multimeter before student entry.',
                'embedding' => $this->perturbVector($safetyVector, 0.014),
            ],
            [
                'title' => 'Workshop Ventilation and Fume Extraction Standards',
                'audience' => Audience::Administrators,
                'summary' => 'OSHA compliance requirements for air filtration and hexavalent chromium fume extraction in trade facilities.',
                'content' => 'Ensuring proper indoor air quality requires daily inspection of source-capture snorkel arms and HEPA filtration units. Facilities must maintain minimum air turnover rates to comply with state trade school health codes.',
                'embedding' => $this->perturbVector($safetyVector, 0.013),
            ],

            // Group 3: Instruction & Evaluation
            [
                'title' => 'Hands-On Practical Exam Rubrics & Grading Standards',
                'audience' => Audience::Teachers,
                'summary' => 'How trade instructors can standardize grading criteria for pipe joint inspections and conduit bending tests.',
                'content' => 'Objective skill evaluations ensure trade apprentices meet industry craftsmanship standards. Utilize our 5-point rubric covering dimensional tolerance, safety compliance, tool technique, and final aesthetic finish.',
                'embedding' => $this->perturbVector($instructionVector, 0.01),
            ],
            [
                'title' => 'Teaching Assistant Mentorship: Supporting At-Risk Apprentices',
                'audience' => Audience::TeachingAssistants,
                'summary' => 'Best practices for TAs running evening lab tutoring sessions for struggling first-year trade students.',
                'content' => 'Many trade apprentices enter with minimal prior mechanical experience. Teaching assistants should host open-lab office hours focused on blueprint reading, math for electricians, and tactile tool ergonomics.',
                'embedding' => $this->perturbVector($instructionVector, 0.015),
            ],

            // Group 4: Alumni & Career Placement
            [
                'title' => 'Transitioning from Trade School to Union Apprenticeship',
                'audience' => Audience::Alumni,
                'summary' => 'Steps for graduating trade students to apply for IBEW and UA local union journeyman programs.',
                'content' => 'Graduating from an accredited vocational program gives you direct interview credit when applying for union apprenticeships. Learn how to submit transcripts, pass the aptitude test, and log your initial on-the-job hours.',
                'embedding' => $this->perturbVector($careerVector, 0.01),
            ],
            [
                'title' => 'Journeyman Licensing Exam Preparation Guide',
                'audience' => Audience::Alumni,
                'summary' => 'Study strategies and codebook navigation tips for passing state trade licensing exams on your first attempt.',
                'content' => 'Passing the National Electrical Code (NEC) or Uniform Plumbing Code (UPC) licensing exam requires fast codebook tabbing and timed calculation practice. Access our alumni study groups and practice exams.',
                'embedding' => $this->perturbVector($careerVector, 0.012),
            ],
        ];

        foreach ($articles as $data) {
            Article::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'title' => $data['title'],
                    'audience' => $data['audience'],
                    'summary' => $data['summary'],
                    'content' => $data['content'],
                    'is_published' => true,
                    'embedding' => $data['embedding'],
                ]
            );
        }
    }

    /**
     * Helper to generate a base 512-dimension float vector with specific cluster characteristics.
     *
     * @return array<int, float>
     */
    protected function createClusterVector(float $biasA, float $biasB): array
    {
        $vector = [];
        for ($i = 0; $i < 512; $i++) {
            $base = ($i % 2 === 0) ? $biasA : $biasB;
            $vector[] = round($base + (sin($i) * 0.02), 6);
        }

        return $vector;
    }

    /**
     * Helper to slightly perturb a cluster vector for close semantic neighbors.
     *
     * @param  array<int, float>  $baseVector
     * @return array<int, float>
     */
    protected function perturbVector(array $baseVector, float $noise): array
    {
        return array_map(function (float $val) use ($noise) {
            return round($val + ((rand(-100, 100) / 1000) * $noise), 6);
        }, $baseVector);
    }
}
