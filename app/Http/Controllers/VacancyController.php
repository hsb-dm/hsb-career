<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class VacancyController extends Controller
{
    public function home(): View
    {
        return view('welcome', ['jobs' => $this->listing()->limit(6)->get()]);
    }

    public function index(): View
    {
        return view('vacancies', ['jobs' => $this->listing()->get()]);
    }

    public function show(string $slug): View
    {
        return view('vacancies-show', [
            'job' => Job::query()->active()->where('slug', $slug)->firstOrFail(),
        ]);
    }

    /** @return Builder<Job> */
    private function listing(): Builder
    {
        return Job::query()
            ->active()
            ->select(['id', 'title', 'slug', 'department', 'location', 'employment_type', 'published_at'])
            ->orderByDesc('published_at');
    }
}
