<?php

use App\Enums\JobStatus;
use App\Models\Applicant;
use App\Models\Job;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', fn () => view('welcome', [
    'jobs' => Job::query()
        ->where('status', JobStatus::Active)
        ->orderByDesc('published_at')
        ->get(),
]))->name('home');

Route::redirect('/login', '/admin/login')->name('login');

Route::middleware('auth')->group(function (): void {
    Route::get('/admin/applicants/{applicant}/cv/preview', function (Applicant $applicant) {
        abort_if(blank($applicant->cv_path) || ! Storage::disk('local')->exists($applicant->cv_path), 404, 'CV file is not available.');
        abort_if(! str_ends_with(strtolower($applicant->cv_path), '.pdf'), 404, 'CV preview is only available for PDF files.');

        return Storage::disk('local')->response($applicant->cv_path, basename($applicant->cv_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($applicant->cv_path).'"',
        ]);
    })->name('admin.applicants.cv.preview');
});
