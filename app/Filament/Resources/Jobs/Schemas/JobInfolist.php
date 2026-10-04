<?php

namespace App\Filament\Resources\Jobs\Schemas;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\WorkArrangement;
use App\Models\Job;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Job information')
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('slug'),
                        TextEntry::make('department'),
                        TextEntry::make('location'),
                        TextEntry::make('employment_type')
                            ->label('Employment type')
                            ->formatStateUsing(fn (EmploymentType $state): string => $state->label()),
                        TextEntry::make('work_arrangement')
                            ->label('Work arrangement')
                            ->formatStateUsing(fn (WorkArrangement $state): string => $state->label()),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (JobStatus $state): string => $state->label())
                            ->color(fn (JobStatus $state): string => $state->color()),
                        TextEntry::make('applicants_count')
                            ->label('Total applicants')
                            ->state(fn (Job $record): int => $record->applicants()->count()),
                        TextEntry::make('published_at')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('closed_at')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Job description')
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->html()
                            ->prose(),
                    ]),
            ]);
    }
}
