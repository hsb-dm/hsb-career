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
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS new_count', [ApplicantStatus::New->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS interview_count', [ApplicantStatus::Interview->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) AS accepted_count', [ApplicantStatus::Accepted->value])
            ->first()?->toArray() ?? [];

        return [
            Stat::make('Total active jobs', Job::query()->where('status', JobStatus::Active)->count())
                ->description('Active jobs')
                ->color('success'),
            Stat::make('Total applicants', (int) ($counts['total'] ?? 0))
                ->description('All applications')
                ->color('gray'),
            Stat::make('New applicants', (int) ($counts['new_count'] ?? 0))
                ->description('Waiting for review')
                ->color('info'),
            Stat::make('Applicants in interview', (int) ($counts['interview_count'] ?? 0))
                ->description('Interview stage')
                ->color('warning'),
            Stat::make('Accepted applicants', (int) ($counts['accepted_count'] ?? 0))
                ->description('Accepted candidates')
                ->color('success'),
        ];
    }
}
