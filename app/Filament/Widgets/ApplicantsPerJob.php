<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ApplicantsPerJob extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Applicants per job')
            ->query(fn (): Builder => Job::query()
                ->withCount('applicants')
                ->orderByDesc('applicants_count')
                ->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('title')
                    ->label('Job'),
                TextColumn::make('department'),
                TextColumn::make('applicants_count')
                    ->label('Applicants'),
            ]);
    }
}
