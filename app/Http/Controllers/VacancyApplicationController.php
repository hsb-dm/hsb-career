<?php

namespace App\Http\Controllers;

use App\Enums\ApplicantStatus;
use App\Enums\JobStatus;
use App\Models\Job;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VacancyApplicationController extends Controller
{
    public function create(string $slug): View
    {
        return view('vacancies-apply', ['job' => $this->activeJob($slug)]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $job = $this->activeJob($slug);

        $choices = [
            'marital_status' => ['Single', 'Married'],
            'current_status' => ['Still Working / employed', 'Available / free / unemployed'],
            'current_domicile' => ['Jakarta Area', 'Bogor Area', 'Depok Area', 'Tangerang Area', 'Bekasi Area'],
            'english_fluency' => ['Professional', 'Conversation', 'Intermediate', 'Beginner'],
            'notice_period' => ['As soon as Possible (ASAP)', '2 weeks notice', '1 month notice', '2 months notice'],
        ];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'current_age' => ['required', 'integer', 'between:1,120'],
            'foreign_language_fluency' => ['nullable', 'string', 'max:255'],
            'current_salary_answer' => ['required', 'string', 'max:255'],
            'additional_benefits' => ['required', 'string', 'max:5000'],
            'expected_salary_answer' => ['required', 'string', 'max:255'],
            'motivation' => ['required', 'string', 'max:5000'],
            'reason_for_leaving' => ['required', 'string', 'max:5000'],
            'latest_company_reference' => ['required', 'string', 'max:5000'],
            'second_latest_company_reference' => ['nullable', 'string', 'max:5000'],
            'third_latest_company_reference' => ['nullable', 'string', 'max:5000'],
            'serious_disease' => ['required', Rule::in(['yes', 'no'])],
            'serious_disease_details' => ['required_if:serious_disease,yes', 'nullable', 'string', 'max:5000'],
        ];

        foreach ($choices as $field => $options) {
            $rules[$field] = ['required', Rule::in([...$options, 'Other'])];
            $rules[$field.'_other'] = ['required_if:'.$field.',Other', 'nullable', 'string', 'max:255'];
        }

        $data = $request->validate($rules);

        foreach (array_keys($choices) as $field) {
            if ($data[$field] === 'Other') {
                $data[$field] = $data[$field.'_other'];
            }
            unset($data[$field.'_other']);
        }

        $data['serious_disease'] = $data['serious_disease'] === 'yes';
        if (! $data['serious_disease']) {
            $data['serious_disease_details'] = null;
        }

        $job->applicants()->create([
            ...$data,
            'status' => ApplicantStatus::New,
            'applied_at' => now(),
        ]);

        return to_route('vacancies.apply', $job->slug)->with('application_success', true);
    }

    private function activeJob(string $slug): Job
    {
        return Job::query()->where('slug', $slug)->where('status', JobStatus::Active)->firstOrFail();
    }
}
