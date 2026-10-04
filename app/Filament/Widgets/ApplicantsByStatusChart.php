<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use Filament\Widgets\PieChartWidget;

class ApplicantsByStatusChart extends PieChartWidget
{
    protected ?string $heading = 'Applicants by status';

    protected int|string|array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $counts = Applicant::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'datasets' => [
                [
                    'data' => collect(ApplicantStatus::cases())
                        ->map(fn (ApplicantStatus $status): int => (int) ($counts[$status->value] ?? 0))
                        ->all(),
                ],
            ],
            'labels' => collect(ApplicantStatus::cases())
                ->map(fn (ApplicantStatus $status): string => $status->label())
                ->all(),
        ];
    }
}
