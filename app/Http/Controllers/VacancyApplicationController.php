<?php

namespace App\Http\Controllers;

use App\Enums\ApplicantStatus;
use App\Models\Job;
use App\Support\ScreeningForm;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VacancyApplicationController extends Controller
{
    public function create(string $slug): View
    {
        return view('vacancies-apply', ['job' => $this->activeJob($slug)]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $job = $this->activeJob($slug);
        $data = ScreeningForm::normalized($request->validate(ScreeningForm::rules()));

        $job->applicants()->create([
            ...$data,
            'status' => ApplicantStatus::New,
            'applied_at' => now(),
        ]);

        return to_route('vacancies.apply', $job->slug)->with('application_success', true);
    }

    private function activeJob(string $slug): Job
    {
        return Job::query()->active()->where('slug', $slug)->firstOrFail();
    }
}
