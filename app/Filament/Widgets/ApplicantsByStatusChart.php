<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use Filament\Widgets\ChartWidget;

class ApplicantsByStatusChart extends ChartWidget
{
    protected ?string $heading = 'Pipeline by stage';

    protected ?string $description = 'Number of applicants in each status.';

    protected ?string $emptyStateHeading = 'No pipeline data yet';

    protected ?string $emptyStateDescription = 'Applicant stages will appear after the first application.';

    protected ?string $maxHeight = '360px';

    protected int|string|array $columnSpan = [
        'default' => 1,
        'xl' => 2,
    ];

    protected function getType(): string
    {
        return 'bar';
    }

    /** @return array<string, mixed> */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
                'y' => ['grid' => ['display' => false]],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $counts = Applicant::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        if ($counts->isEmpty()) {
            return [];
        }

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
