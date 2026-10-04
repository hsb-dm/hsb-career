<?php

namespace App\Filament\Resources\Applicants\Schemas;

use App\Enums\ApplicantStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                            ->preload()
                            ->required(),
                        Select::make('status')
                            ->options(ApplicantStatus::options())
                            ->default(ApplicantStatus::New->value)
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
                        FileUpload::make('cv_path')
                            ->label('CV file')
                            ->disk('local')
                            ->directory('cvs')
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            ])
                            ->maxSize(5120)
                            ->downloadable()
                            ->openable(),
                    ]),
                Section::make('Internal HR note')
                    ->schema([
                        Textarea::make('hr_note')
                            ->label('HR note')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
