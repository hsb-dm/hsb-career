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
                        TextEntry::make('current_age')->label('Current Age')->placeholder('-'),
                        TextEntry::make('marital_status')->label('Marital Status')->placeholder('-'),
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
                            ->state(fn (Applicant $record): string => $record->current_salary_answer ?? Rupiah::format($record->current_salary)),
                        TextEntry::make('expected_salary')
                            ->label('Expected salary')
                            ->state(fn (Applicant $record): string => $record->expected_salary_answer ?? Rupiah::format($record->expected_salary)),
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
                            ->label('Join Notification'),
                        TextEntry::make('current_status')->label('Current Status')->placeholder('-'),
                        TextEntry::make('current_domicile')->label('Current Domicile')->placeholder('-'),
                        TextEntry::make('english_fluency')->label('English Fluency')->placeholder('-'),
                        TextEntry::make('foreign_language_fluency')->label('Foreign Language Fluency (Other than English)')->placeholder('-'),
                        TextEntry::make('additional_benefits')->label('Additional Benefits')->placeholder('-'),
                        TextEntry::make('motivation')->label('Motivation for Apply')->placeholder('-'),
                        TextEntry::make('reason_for_leaving')->label('Reason of Leaving Last Company')->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('Reference checks')
                    ->schema([
                        TextEntry::make('latest_company_reference')->label('Latest Company')->placeholder('-'),
                        TextEntry::make('second_latest_company_reference')->label('2nd Latest Company')->placeholder('-'),
                        TextEntry::make('third_latest_company_reference')->label('3rd Latest Company')->placeholder('-'),
                    ]),
                Section::make('Health')
                    ->schema([
                        TextEntry::make('serious_disease')->label('Diagnosed with serious disease')->formatStateUsing(fn (?bool $state): string => $state === null ? '-' : ($state ? 'Yes' : 'No')),
                        TextEntry::make('serious_disease_details')->label('Illness / disease details')->placeholder('-'),
                    ]),
                Section::make('CV')
                    ->schema([
                        TextEntry::make('cv_path')
                            ->label('File name')
                            ->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-'),
                        TextEntry::make('cv_available')
                            ->label('Availability')
                            ->badge()
                            ->state(fn (Applicant $record): string => blank($record->cv_path) ? 'Not provided' : (Storage::disk('local')->exists($record->cv_path) ? 'Available' : 'File not found'))
                            ->color(fn (string $state): string => $state === 'Available' ? 'success' : ($state === 'Not provided' ? 'gray' : 'warning')),
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
