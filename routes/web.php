<?php

use App\Http\Controllers\CvPreviewController;
use App\Http\Controllers\TalentPoolController;
use App\Http\Controllers\VacancyApplicationController;
use App\Http\Controllers\VacancyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [VacancyController::class, 'home'])->name('home');
Route::get('/vacancies', [VacancyController::class, 'index'])->name('vacancies.index');
Route::get('/vacancies/{slug}', [VacancyController::class, 'show'])->name('vacancies.show');

Route::get('/vacancies/{slug}/apply', [VacancyApplicationController::class, 'create'])->name('vacancies.apply');
Route::post('/vacancies/{slug}/apply', [VacancyApplicationController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('vacancies.apply.store');

Route::post('/talent-pool', [TalentPoolController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('talent-pool.store');

Route::redirect('/login', '/admin/login')->name('login');

Route::middleware('auth')->group(function (): void {
    Route::get('/admin/talent-pool/{entry}/cv/preview', [CvPreviewController::class, 'talentPool'])
        ->name('admin.talent-pool.cv.preview');
    Route::get('/admin/applicants/{applicant}/cv/preview', [CvPreviewController::class, 'applicant'])
        ->name('admin.applicants.cv.preview');
});
