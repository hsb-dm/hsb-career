<?php

namespace Database\Seeders;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\TalentPoolEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class RecruitmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Job::query()->withCount('applicants')->get() as $job) {
            for ($slot = $job->applicants_count + 1; $slot <= 5; $slot++) {
                Applicant::factory()->for($job)->create([
                    'name' => "Demo Applicant {$job->id}-{$slot}",
                    'email' => "demo-applicant-{$job->id}-{$slot}@example.test",
                    'cv_path' => null,
                    'status' => ApplicantStatus::cases()[($slot - 1) % count(ApplicantStatus::cases())],
                ]);
            }
        }

        $existingEntries = TalentPoolEntry::query()->count();

        if ($existingEntries >= 5) {
            return;
        }

        $cvPath = 'talent-pool-cvs/demo-cv.pdf';
        if (! Storage::disk('local')->exists($cvPath)) {
            Storage::disk('local')->put($cvPath, $this->demoPdf());
        }

        for ($slot = $existingEntries + 1; $slot <= 5; $slot++) {
            TalentPoolEntry::query()->create([
                'name' => "Demo Talent {$slot}",
                'email' => "demo-talent-{$slot}@example.test",
                'area_of_interest' => TalentPoolEntry::AREAS_OF_INTEREST[$slot - 1],
                'cv_path' => $cvPath,
            ]);
        }
    }

    private function demoPdf(): string
    {
        $content = 'BT /F1 14 Tf 50 750 Td (Demo CV - test data only) Tj ET';
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            "<< /Length ".strlen($content)." >>\nstream\n{$content}\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf('%010d 00000 n ', $offset)."\n";
        }

        return $pdf."trailer\n<< /Size 6 /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF\n";
    }
}
