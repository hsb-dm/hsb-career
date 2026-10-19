<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\TalentPoolEntry;
use App\Support\CvStorage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvPreviewController extends Controller
{
    public function applicant(Applicant $applicant): StreamedResponse
    {
        return $this->preview($applicant->cv_path);
    }

    public function applicantDownload(Applicant $applicant): StreamedResponse
    {
        $path = $applicant->cv_path;
        $disk = CvStorage::readDisk($path);
        abort_if($disk === null, 404, 'CV file is not available.');

        return Storage::disk($disk)->download($path, basename($path));
    }

    public function talentPool(TalentPoolEntry $entry): StreamedResponse
    {
        return $this->preview($entry->cv_path);
    }

    private function preview(?string $path): StreamedResponse
    {
        $disk = CvStorage::readDisk($path);
        abort_if($disk === null, 404, 'CV file is not available.');
        abort_unless(str_ends_with(strtolower($path), '.pdf'), 404, 'CV preview is only available for PDF files.');

        return Storage::disk($disk)->response($path, basename($path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }
}
