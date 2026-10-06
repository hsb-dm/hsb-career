<?php

namespace App\Filament\Resources\Applicants\Schemas;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApplicantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Personal information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        DateTimePicker::make('applied_at')
                            ->label('Applied at')
                            ->seconds(false)
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Application information')
                    ->schema([
                        Select::make('job_id')
                            ->label('Job')
                            ->relationship('job', 'title')
                            ->searchable()
                            ->required(),
                        Select::make('status')
                            ->options(ApplicantStatus::options())
                            ->default(ApplicantStatus::AiAtsScreened->value)
                            ->required(),
                        TextInput::make('current_salary')
                            ->label('Current salary')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->nullable(),
                        TextInput::make('expected_salary')
                            ->label('Expected salary')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->required(),
                        TextInput::make('notice_period')
                            ->label('Notice period')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('CV')
                    ->schema([
                        TextEntry::make('cv_path')
                            ->label('CV file')
                            ->state(fn (Applicant $record): string => basename($record->cv_path)),
                    ])
                    ->visible(fn (?Applicant $record): bool => filled($record?->cv_path)),
            ]);
    }
}
