<?php

namespace App\Filament\Resources\Jobs\Tables;

use App\Enums\JobStatus;
use App\Models\Job;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (JobStatus|string|null $state): ?string => $state instanceof JobStatus ? $state->label() : $state)
                    ->color(fn (JobStatus|string|null $state): string => $state instanceof JobStatus ? $state->color() : 'gray'),
                TextColumn::make('applicants_count')
                    ->counts('applicants')
                    ->label('Applicants')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->options(fn () => Job::query()
                        ->select('department')
                        ->distinct()
                        ->orderBy('department')
                        ->pluck('department', 'department')
                        ->all()),
                SelectFilter::make('status')
                    ->options(JobStatus::options()),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
