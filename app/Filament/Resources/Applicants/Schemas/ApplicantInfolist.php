<?php

namespace App\Filament\Resources\Applicants\Schemas;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use App\Support\Rupiah;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicantInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Personal information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                        TextEntry::make('applied_at')
                            ->label('Applied at')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
                Section::make('Application information')
                    ->schema([
                        TextEntry::make('job.title')
                            ->label('Job'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (ApplicantStatus $state): string => $state->label())
                            ->color(fn (ApplicantStatus $state): string => $state->color()),
                        TextEntry::make('current_salary')
                            ->label('Current salary')
                            ->formatStateUsing(fn (?int $state): string => Rupiah::format($state)),
                        TextEntry::make('expected_salary')
                            ->label('Expected salary')
                            ->formatStateUsing(fn (?int $state): string => Rupiah::format($state)),
                        TextEntry::make('salary_increase')
                            ->label('Salary increase')
                            ->state(function (Applicant $record): string {
                                $increase = $record->salaryIncrease();

                                if ($increase === null) {
                                    return '-';
                                }

                                $percentage = $record->salaryIncreasePercentage();

                                return Rupiah::format($increase).($percentage === null ? '' : ' ('.number_format($percentage, 1).'%)');
                            }),
                        TextEntry::make('notice_period')
                            ->label('Notice period'),
                    ])
                    ->columns(2),
                Section::make('CV')
                    ->schema([
                        TextEntry::make('cv_path')
                            ->label('File name')
                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-'),
                        TextEntry::make('cv_available')
                            ->label('Availability')
                            ->badge()
                            ->state(fn (Applicant $record): string => filled($record->cv_path) && Storage::disk('local')->exists($record->cv_path) ? 'Available' : 'File not found')
                            ->color(fn (string $state): string => $state === 'Available' ? 'success' : 'warning'),
                        TextEntry::make('cv_preview')
                            ->label('Preview')
                            ->state(fn (Applicant $record): string => filled($record->cv_path) && Storage::disk('local')->exists($record->cv_path) && Str::endsWith(strtolower($record->cv_path), '.pdf') ? 'PDF preview available' : '-'),
                    ])
                    ->columns(3),
                Section::make('Internal HR note')
                    ->schema([
                        TextEntry::make('hr_note')
                            ->hiddenLabel()
                            ->placeholder('-')
                            ->prose(),
                    ]),
            ]);
    }
}
