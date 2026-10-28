<?php

namespace App\Filament\Resources\Jobs\Schemas;

use App\Enums\EmploymentType;
use App\Enums\JobStatus;
use App\Enums\WorkArrangement;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Job information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('department')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                        Select::make('employment_type')
                            ->label('Employment type')
                            ->options(EmploymentType::options())
                            ->required(),
                        Select::make('work_arrangement')
                            ->label('Work arrangement')
                            ->options(WorkArrangement::options())
                            ->required(),
                        Toggle::make('status')
                            ->label('Active')
                            ->helperText('')
                            ->default(true)
                            ->formatStateUsing(fn (JobStatus|string|bool|null $state): bool => $state === true || $state === JobStatus::Active || $state === JobStatus::Active->value)
                            ->dehydrateStateUsing(fn (bool $state): string => $state ? JobStatus::Active->value : JobStatus::Inactive->value)
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('Job content')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Job description')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
