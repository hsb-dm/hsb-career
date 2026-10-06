<?php

namespace Database\Seeders;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\WorkArrangement;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'HR Admin', 'password' => Hash::make('password')],
        );

        $jobs = [
            ['Legal & Compliance Manager', 'Compliance'],
            ['IT Manager', 'IT'],
            ['Product Manager', 'Product'],
            ['Performance Marketing', 'Digital Marketing'],
            ['CRM & Growth', 'Digital Marketing'],
            ['Brand Marketing Specialist', 'Digital Marketing'],
            ['SEO & Media Partnership', 'Digital Marketing'],
            ['Brand Partnership', 'Business Development'],
            ['Trading Operation', 'Dealing'],
            ['Head Of FAT (Mandarin Speaker)', 'Finance Accounting & Tax'],
        ];

        $legalDescription = <<<'HTML'
<h2>About the Role</h2>
<p>We are looking for an experienced Legal &amp; Compliance Manager to lead our legal and compliance functions at HSB. In this role, you will ensure that our business operations comply with applicable regulatory requirements while supporting the company's strategic growth.</p>
<p>You will work closely with management and cross-functional teams, oversee regulatory matters, manage legal risks, and maintain effective relationships with regulatory authorities, including BAPPEBTI, OJK, and ICDX.</p>
<p>The ideal candidate has a strong background in legal and regulatory compliance, particularly within the financial services or fintech industry, along with excellent analytical, communication, and problem-solving skills.</p>
<h2>Key Responsibilities</h2>
<ul>
<li>Legal &amp; Compliance Management: Lead and oversee the company's legal and compliance functions, ensuring business operations adhere to applicable laws, regulations, and industry standards.</li>
<li>Regulatory Relations: Serve as the primary liaison with regulatory authorities, including BAPPEBTI, OJK, and ICDX, managing regulatory submissions, audits, and official correspondence.</li>
<li>Contract Management: Draft, review, and negotiate commercial agreements, non-disclosure agreements (NDAs), partnership agreements, and other legal documents to protect the company's interests.</li>
<li>Regulatory Compliance: Monitor regulatory developments and ensure company policies, procedures, and business practices remain aligned with the latest applicable requirements.</li>
<li>Risk Management: Identify, assess, and mitigate legal and regulatory risks while developing effective compliance strategies and internal controls.</li>
<li>Complaint Resolution: Handle escalated client complaints involving legal or regulatory matters and coordinate appropriate resolutions.</li>
<li>Cross-functional Collaboration: Work closely with management and relevant departments to provide legal guidance and ensure compliance across business operations.</li>
<li>Policy Development: Establish, review, and update internal legal and compliance policies to support business objectives and regulatory requirements.</li>
</ul>
<h2>Requirements</h2>
<ul>
<li>Bachelor's degree in Law (LL.B. or equivalent) from a recognized university.</li>
<li>At least 3–5 years of experience in legal and compliance roles, preferably within financial services, fintech, banking, securities, or brokerage firms.</li>
<li>Strong knowledge of Indonesian financial regulations, particularly those issued by BAPPEBTI and OJK, as well as relevant ICDX requirements.</li>
<li>Proven experience handling regulatory submissions, audits, and correspondence with regulatory authorities.</li>
<li>Demonstrated experience drafting, reviewing, and negotiating commercial agreements, NDAs, and other legal documents.</li>
<li>Strong understanding of corporate governance, regulatory risk management, and compliance frameworks.</li>
<li>Excellent analytical, communication, negotiation, and problem-solving skills.</li>
<li>Proficiency in written and spoken English.</li>
</ul>
HTML;

        foreach ($jobs as $index => [$title, $department]) {
            Job::query()->updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'department' => $department,
                    'location' => 'Jakarta',
                    'employment_type' => EmploymentType::FullTime,
                    'work_arrangement' => WorkArrangement::Onsite,
                    'description' => $index === 0
                        ? $legalDescription
                        : "<p>Join the {$department} team at HSB Investasi as our {$title}.</p>",
                    'status' => JobStatus::Active,
                    'published_at' => Carbon::create(2026, 9, 29, 9, 0)->subMinutes($index),
                    'closed_at' => null,
                ],
            );
        }

        $this->call(RecruitmentDemoSeeder::class);
    }
}
