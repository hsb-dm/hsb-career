<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\TalentPoolEntry;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvPreviewController extends Controller
{
    public function applicant(Applicant $applicant): StreamedResponse
    {
        return $this->preview($applicant->cv_path);
    }

    public function talentPool(TalentPoolEntry $entry): StreamedResponse
    {
        return $this->preview($entry->cv_path);
    }

    private function preview(?string $path): StreamedResponse
    {
        abort_if(blank($path) || ! Storage::disk('local')->exists($path), 404, 'CV file is not available.');
        abort_unless(str_ends_with(strtolower($path), '.pdf'), 404, 'CV preview is only available for PDF files.');

        return Storage::disk('local')->response($path, basename($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
