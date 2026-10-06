<?php

namespace App\Http\Controllers;

use App\Enums\ApplicantStatus;
use App\Models\Job;
use App\Support\ScreeningForm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class VacancyApplicationController extends Controller
{
    public function create(string $slug): View
    {
        return view('vacancies-apply', ['job' => $this->activeJob($slug)]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $job = $this->activeJob($slug);
        $validated = $request->validate(
            ScreeningForm::rules(),
            ScreeningForm::messages(),
            ScreeningForm::attributes(),
        );
        $cv = $validated['cv'];
        unset($validated['cv']);
        $data = ScreeningForm::normalized($validated);
        $cvPath = $cv->store('cvs', 'local');
        if ($cvPath === false) {
            throw new RuntimeException('Unable to store the uploaded CV.');
        }

        try {
            $job->applicants()->create([
                ...$data,
                'cv_path' => $cvPath,
                'status' => ApplicantStatus::AiAtsScreened,
                'applied_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($cvPath);

            throw $exception;
        }

        return to_route('vacancies.apply', $job->slug)->with('application_success', true);
    }

    private function activeJob(string $slug): Job
    {
        return Job::query()->active()->where('slug', $slug)->firstOrFail();
    }
}
