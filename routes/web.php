<?php

use App\Enums\JobStatus;
use App\Http\Controllers\TalentPoolController;
use App\Http\Controllers\VacancyApplicationController;
use App\Models\Applicant;
use App\Models\Job;
use App\Models\TalentPoolEntry;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', fn () => view('welcome', [
    'jobs' => Job::query()
        ->where('status', JobStatus::Active)
        ->orderByDesc('published_at')
        ->limit(6)
        ->get(),
]))->name('home');

Route::get('/vacancies', fn () => view('vacancies', [
    'jobs' => Job::query()
        ->where('status', JobStatus::Active)
        ->orderByDesc('published_at')
        ->get(),
]))->name('vacancies.index');

Route::get('/vacancies/{slug}', function (string $slug) {
    $job = Job::query()
        ->where('slug', $slug)
        ->where('status', JobStatus::Active)
        ->firstOrFail();

    return view('vacancies-show', ['job' => $job]);
})->name('vacancies.show');

Route::get('/vacancies/{slug}/apply', [VacancyApplicationController::class, 'create'])->name('vacancies.apply');
Route::post('/vacancies/{slug}/apply', [VacancyApplicationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('vacancies.apply.store');

Route::post('/talent-pool', [TalentPoolController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('talent-pool.store');

Route::redirect('/login', '/admin/login')->name('login');

Route::middleware('auth')->group(function (): void {
    Route::get('/admin/talent-pool/{entry}/cv/preview', function (TalentPoolEntry $entry) {
        abort_unless(Storage::disk('local')->exists($entry->cv_path), 404, 'CV file is not available.');
        abort_unless(str_ends_with(strtolower($entry->cv_path), '.pdf'), 404, 'CV preview is only available for PDF files.');

        return Storage::disk('local')->response($entry->cv_path, basename($entry->cv_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($entry->cv_path).'"',
        ]);
    })->name('admin.talent-pool.cv.preview');

    Route::get('/admin/applicants/{applicant}/cv/preview', function (Applicant $applicant) {
        abort_if(blank($applicant->cv_path) || ! Storage::disk('local')->exists($applicant->cv_path), 404, 'CV file is not available.');
        abort_if(! str_ends_with(strtolower($applicant->cv_path), '.pdf'), 404, 'CV preview is only available for PDF files.');

        return Storage::disk('local')->response($applicant->cv_path, basename($applicant->cv_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.basename($applicant->cv_path).'"',
        ]);
    })->name('admin.applicants.cv.preview');
});
