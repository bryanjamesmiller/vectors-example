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
        $fixturePath = database_path('seeders/data/articles.json');

        if (file_exists($fixturePath)) {
            $jsonContent = file_get_contents($fixturePath);
            if ($jsonContent !== false) {
                /** @var list<array{title: string, audience: string, summary: ?string, content: string, is_published?: bool, embedding?: ?list<float>}>|null $articlesData */
                $articlesData = json_decode($jsonContent, true);

                if (is_array($articlesData) && ! empty($articlesData)) {
                    foreach ($articlesData as $data) {
                        Article::updateOrCreate(
                            ['slug' => Str::slug($data['title'])],
                            [
                                'title' => $data['title'],
                                'audience' => Audience::from($data['audience']),
                                'summary' => $data['summary'] ?? null,
                                'content' => $data['content'],
                                'is_published' => $data['is_published'] ?? true,
                                'embedding' => $data['embedding'] ?? null,
                            ]
                        );
                    }

                    return;
                }
            }
        }

        // 5 Core Topic Vectors (512 dims)
        $topicSafety = $this->createBaseVector(10, 0.08, -0.05);
        $topicFinancialAid = $this->createBaseVector(20, -0.07, 0.09);
        $topicInstruction = $this->createBaseVector(30, 0.06, 0.07);
        $topicCareer = $this->createBaseVector(40, -0.08, -0.06);
        $topicCompliance = $this->createBaseVector(50, 0.02, 0.09);

        // 6 Audience Vectors (512 dims)
        $audStudents = $this->createBaseVector(100, 0.05, 0.04);
        $audRecruits = $this->createBaseVector(200, 0.03, 0.06);
        $audAlumni = $this->createBaseVector(300, -0.05, -0.03);
        $audTAs = $this->createBaseVector(400, 0.02, -0.05);
        $audTeachers = $this->createBaseVector(500, 0.04, -0.06);
        $audAdmins = $this->createBaseVector(600, -0.02, 0.07);

        $articles = [
            // ==================== STUDENTS ====================
            [
                'title' => 'Personal Protective Equipment (PPE) Guidelines for Welding Labs',
                'audience' => Audience::Students,
                'summary' => 'Mandatory safety gear, shade ratings, and fire-retardant clothing requirements for welding workshops.',
                'content' => 'Safety in the welding lab starts with proper personal protective equipment. Students must wear auto-darkening helmets with minimum shade 10-12, leather gauntlet gloves, flame-resistant jackets, and steel-toe boots at all times.',
                'topic' => $topicSafety,
                'audience_vec' => $audStudents,
            ],
            [
                'title' => 'Electrical Workshop Hand Tool Safety & Insulation Rules',
                'audience' => Audience::Students,
                'summary' => 'Proper handling of 1000V rated insulated hand tools and testing equipment in beginner electrical labs.',
                'content' => 'Always inspect your insulated screwdrivers and wire strippers for nicks or cracks before beginning energized lab work. Never use uninsulated tools in the electrical training booths.',
                'topic' => $topicSafety,
                'audience_vec' => $audStudents,
            ],
            [
                'title' => 'Workshop Chemical Hazard & Fire Extinguisher Protocol',
                'audience' => Audience::Students,
                'summary' => 'Location and operation of eye-wash stations, Class ABC/D fire extinguishers, and flammable storage cabinets.',
                'content' => 'Every student must memorize the locations of emergency stop buttons and eye-wash stations. Solvents and cutting oils must be returned to yellow flammable storage lockers immediately after use.',
                'topic' => $topicSafety,
                'audience_vec' => $audStudents,
            ],
            [
                'title' => 'Applying for Trade Tool Grants & Fee Waivers',
                'audience' => Audience::Students,
                'summary' => 'How trade school students can apply for state and private grants to cover expensive equipment and toolsets.',
                'content' => 'Trade school programs often require specialized equipment, such as welding hoods, multimeter testers, and power toolsets. This guide covers how to apply for institutional grant waivers and local foundation stipends.',
                'topic' => $topicFinancialAid,
                'audience_vec' => $audStudents,
            ],
            [
                'title' => 'Emergency Student Aid & Housing Relief Grants',
                'audience' => Audience::Students,
                'summary' => 'Access short-term emergency financial assistance for room, board, and unexpected trade school expenses.',
                'content' => 'When unexpected financial hardships arise, students can request emergency micro-grants for housing, food security, and transportation vouchers. Applications are reviewed confidentially within 48 hours.',
                'topic' => $topicFinancialAid,
                'audience_vec' => $audStudents,
            ],
            [
                'title' => 'Campus Tool Rental & Equipment Locker Checkout',
                'audience' => Audience::Students,
                'summary' => 'Procedures for checking out specialized hydraulic pipe benders and rotary hammer drills from the student tool crib.',
                'content' => 'Students who have not yet purchased specialized tools can reserve tool crib equipment using their student ID badge. All tools must be returned clean and inspected before shop closing.',
                'topic' => $topicSafety,
                'audience_vec' => $audStudents,
            ],

            // ==================== RECRUITS ====================
            [
                'title' => 'Veteran Financial Assistance & GI Bill Tuition Coverage',
                'audience' => Audience::Recruits,
                'summary' => 'Comprehensive breakdown of veteran education benefits for electrical and plumbing vocational certifications.',
                'content' => 'Military veterans entering trade school can leverage Post-9/11 GI Bill and VR&E programs to cover full tuition, housing allowances, and monthly tool stipends. Learn how to verify program eligibility with the campus VA certifying official.',
                'topic' => $topicFinancialAid,
                'audience_vec' => $audRecruits,
            ],
            [
                'title' => 'High School to Trade School: Application & Admissions Checklist',
                'audience' => Audience::Recruits,
                'summary' => 'Step-by-step enrollment timeline, transcript requirements, and campus workshop tours for prospective recruits.',
                'content' => 'Getting started at our trade institute involves submitting your high school transcript, scheduling a hands-on shop tour, and completing your FAFSA financial aid profile.',
                'topic' => $topicCareer,
                'audience_vec' => $audRecruits,
            ],
            [
                'title' => 'Comparing Apprenticeship Pathways: Electrical vs. Welding vs. HVAC',
                'audience' => Audience::Recruits,
                'summary' => 'Detailed comparison of entry wages, physical demands, and career growth trajectories across trade sectors.',
                'content' => 'Choosing the right trade depends on your interests. Electrical offers strong industrial automation paths, Welding excels in fabrication and pipeline work, and HVAC provides high residential demand.',
                'topic' => $topicCareer,
                'audience_vec' => $audRecruits,
            ],

            // ==================== TEACHING ASSISTANTS ====================
            [
                'title' => 'High Voltage Electrical Lab Lockout/Tagout (LOTO) Procedures',
                'audience' => Audience::TeachingAssistants,
                'summary' => 'Standard operating procedures for de-energizing industrial circuits before student laboratory exercises.',
                'content' => 'Teaching assistants and lab technicians must ensure all three-phase electrical trainers are locked out and tagged out prior to student wiring exercises. Verify zero voltage with a calibrated CAT III multimeter before student entry.',
                'topic' => $topicSafety,
                'audience_vec' => $audTAs,
            ],
            [
                'title' => 'Teaching Assistant Mentorship: Supporting At-Risk Apprentices',
                'audience' => Audience::TeachingAssistants,
                'summary' => 'Best practices for TAs running evening lab tutoring sessions for struggling first-year trade students.',
                'content' => 'Many trade apprentices enter with minimal prior mechanical experience. Teaching assistants should host open-lab office hours focused on blueprint reading, math for electricians, and tactile tool ergonomics.',
                'topic' => $topicInstruction,
                'audience_vec' => $audTAs,
            ],
            [
                'title' => 'Managing Lab Equipment Inventories and Calibration Logs',
                'audience' => Audience::TeachingAssistants,
                'summary' => 'Maintaining inspection logs for torque wrenches, multimeters, and gas flow meters in the department toolroom.',
                'content' => 'Teaching assistants must log daily equipment calibrations and quarantine damaged power cords or cracked welding cables immediately.',
                'topic' => $topicSafety,
                'audience_vec' => $audTAs,
            ],

            // ==================== TEACHERS ====================
            [
                'title' => 'Hands-On Practical Exam Rubrics & Grading Standards',
                'audience' => Audience::Teachers,
                'summary' => 'How trade instructors can standardize grading criteria for pipe joint inspections and conduit bending tests.',
                'content' => 'Objective skill evaluations ensure trade apprentices meet industry craftsmanship standards. Utilize our 5-point rubric covering dimensional tolerance, safety compliance, tool technique, and final aesthetic finish.',
                'topic' => $topicInstruction,
                'audience_vec' => $audTeachers,
            ],
            [
                'title' => 'Lesson Planning for 40-Hour Shop Intensive Weeks',
                'audience' => Audience::Teachers,
                'summary' => 'Structuring practical workshop time, safety briefings, and blueprint analysis for vocational instructors.',
                'content' => 'Shop intensive weeks require balancing 20% theory with 80% tactile execution. Start each morning with a 15-minute tailgate safety meeting and skill demonstration.',
                'topic' => $topicInstruction,
                'audience_vec' => $audTeachers,
            ],

            // ==================== ALUMNI ====================
            [
                'title' => 'Transitioning from Trade School to Union Apprenticeship',
                'audience' => Audience::Alumni,
                'summary' => 'Steps for graduating trade students to apply for IBEW and UA local union journeyman programs.',
                'content' => 'Graduating from an accredited vocational program gives you direct interview credit when applying for union apprenticeships. Learn how to submit transcripts, pass the aptitude test, and log your initial on-the-job hours.',
                'topic' => $topicCareer,
                'audience_vec' => $audAlumni,
            ],
            [
                'title' => 'Journeyman Licensing Exam Preparation Guide',
                'audience' => Audience::Alumni,
                'summary' => 'Study strategies and codebook navigation tips for passing state trade licensing exams on your first attempt.',
                'content' => 'Passing the National Electrical Code (NEC) or Uniform Plumbing Code (UPC) licensing exam requires fast codebook tabbing and timed calculation practice. Access our alumni study groups and practice exams.',
                'topic' => $topicCareer,
                'audience_vec' => $audAlumni,
            ],

            // ==================== ADMINISTRATORS ====================
            [
                'title' => 'Workshop Ventilation and Fume Extraction Standards',
                'audience' => Audience::Administrators,
                'summary' => 'OSHA compliance requirements for air filtration and hexavalent chromium fume extraction in trade facilities.',
                'content' => 'Ensuring proper indoor air quality requires daily inspection of source-capture snorkel arms and HEPA filtration units. Facilities must maintain minimum air turnover rates to comply with state trade school health codes.',
                'topic' => $topicCompliance,
                'audience_vec' => $audAdmins,
            ],
            [
                'title' => 'Annual Trade Program Accreditation & OSHA Audit Checklist',
                'audience' => Audience::Administrators,
                'summary' => 'Documentation guidelines for state vocational board reviews and shop safety certifications.',
                'content' => 'Preparing for accreditation requires consolidating instructor certifications, student lab hours, equipment maintenance logs, and graduate job placement metrics.',
                'topic' => $topicCompliance,
                'audience_vec' => $audAdmins,
            ],
        ];

        foreach ($articles as $data) {
            // Combine 70% topic + 30% audience vector with slight unique noise for realistic, rich cosine clustering
            $embedding = $this->blendVectors($data['topic'], $data['audience_vec'], 0.70, 0.30);

            Article::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'title' => $data['title'],
                    'audience' => $data['audience'],
                    'summary' => $data['summary'],
                    'content' => $data['content'],
                    'is_published' => true,
                    'embedding' => $embedding,
                ]
            );
        }
    }

    /**
     * Helper to generate a normalized base float vector (512 dimensions).
     *
     * @return array<int, float>
     */
    protected function createBaseVector(int $seedOffset, float $primaryBias, float $secondaryBias): array
    {
        $vector = [];
        for ($i = 0; $i < 512; $i++) {
            $val = ($i % 3 === 0)
                ? $primaryBias + (cos(($i + $seedOffset) * 0.1) * 0.04)
                : $secondaryBias + (sin(($i + $seedOffset) * 0.1) * 0.04);
            $vector[] = round($val, 6);
        }

        return $vector;
    }

    /**
     * Blend topic and audience vectors with proportional weighting.
     *
     * @param  array<int, float>  $vecA
     * @param  array<int, float>  $vecB
     * @return array<int, float>
     */
    protected function blendVectors(array $vecA, array $vecB, float $weightA, float $weightB): array
    {
        $blended = [];
        for ($i = 0; $i < 512; $i++) {
            $noise = (rand(-50, 50) / 10000); // 0.005 subtle noise
            $val = ($vecA[$i] * $weightA) + ($vecB[$i] * $weightB) + $noise;
            $blended[] = round($val, 6);
        }

        return $blended;
    }
}
