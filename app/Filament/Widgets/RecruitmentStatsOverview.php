<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicantStatus;
use App\Enums\JobStatus;
use App\Models\Applicant;
use App\Models\Job;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RecruitmentStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Recruitment overview';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $counts = Applicant::query()
            ->selectRaw('COUNT(*) AS total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS screened_count', [ApplicantStatus::AiAtsScreened->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS hr_interview_count', [ApplicantStatus::HrInterview->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS hired_count', [ApplicantStatus::Hired->value])
            ->first()?->toArray() ?? [];

        return [
            Stat::make('Total active jobs', Job::query()->where('status', JobStatus::Active)->count())
                ->description('Active jobs')
                ->color('success'),
            Stat::make('Total applicants', (int) ($counts['total'] ?? 0))
                ->description('All applications')
                ->color('gray'),
            Stat::make('AI ATS Screened', (int) ($counts['screened_count'] ?? 0))
                ->description('Screening stage')
                ->color('info'),
            Stat::make('HR Interview', (int) ($counts['hr_interview_count'] ?? 0))
                ->description('HR interview stage')
                ->color('warning'),
            Stat::make('Hired', (int) ($counts['hired_count'] ?? 0))
                ->description('Hired candidates')
                ->color('success'),
        ];
    }
}
