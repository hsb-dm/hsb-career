<?php

use App\Http\Controllers\CvPreviewController;
use App\Http\Controllers\TalentPoolController;
use App\Http\Controllers\VacancyApplicationController;
use App\Http\Controllers\VacancyController;
use App\Http\Middleware\NoIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', [VacancyController::class, 'home'])->name('home');
Route::get('/vacancies', [VacancyController::class, 'index'])->name('vacancies.index');
Route::get('/vacancies/{slug}', [VacancyController::class, 'show'])->name('vacancies.show');

Route::get('/sitemap.xml', [VacancyController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', fn () => response("User-agent: *\nAllow: /\nSitemap: ".route('sitemap')."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']));

Route::get('/vacancies/{slug}/apply', [VacancyApplicationController::class, 'create'])->middleware(NoIndex::class)->name('vacancies.apply');
Route::post('/vacancies/{slug}/apply', [VacancyApplicationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('vacancies.apply.store');

Route::post('/talent-pool', [TalentPoolController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('talent-pool.store');

Route::redirect('/login', '/admin/login')->name('login');

Route::middleware(['auth', NoIndex::class])->group(function (): void {
    Route::get('/admin/talent-pool/{entry}/cv/preview', [CvPreviewController::class, 'talentPool'])
        ->name('admin.talent-pool.cv.preview');
    Route::get('/admin/applicants/{applicant}/cv/preview', [CvPreviewController::class, 'applicant'])
        ->name('admin.applicants.cv.preview');
    Route::get('/admin/applicants/{applicant}/cv/download', [CvPreviewController::class, 'applicantDownload'])
        ->name('admin.applicants.cv.download');
});
