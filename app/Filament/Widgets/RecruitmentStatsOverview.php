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
        return [
            Stat::make('Total active jobs', Job::query()->where('status', JobStatus::Active)->count())
                ->description('Active jobs')
                ->color('success'),
            Stat::make('Total applicants', Applicant::query()->count())
                ->description('All applications')
                ->color('gray'),
            Stat::make('New applicants', Applicant::query()->where('status', ApplicantStatus::New)->count())
                ->description('Waiting for review')
                ->color('info'),
            Stat::make('Applicants in interview', Applicant::query()->where('status', ApplicantStatus::Interview)->count())
                ->description('Interview stage')
                ->color('warning'),
            Stat::make('Accepted applicants', Applicant::query()->where('status', ApplicantStatus::Accepted)->count())
                ->description('Accepted candidates')
                ->color('success'),
        ];
    }
}
