<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Jobs\JobResource;
use App\Models\Job;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ApplicantsPerJob extends TableWidget
{
    protected int|string|array $columnSpan = ['default' => 1, 'xl' => 1];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Busiest vacancies')
            ->description('Top five roles by application volume.')
            ->emptyStateHeading('No vacancies yet')
            ->emptyStateDescription('Create a vacancy to start tracking applications.')
            ->query(fn (): Builder => Job::query()
                ->withCount('applicants')
                ->orderByDesc('applicants_count')
                ->limit(5))
            ->paginated(false)
            ->recordUrl(fn (Job $record): string => JobResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('title')
                    ->label('Job'),
                TextColumn::make('applicants_count')
                    ->label('Applicants'),
            ]);
    }
}
