<?php

namespace App\Filament\Exports;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use Carbon\CarbonInterface;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ApplicantExporter extends Exporter
{
    protected static ?string $model = Applicant::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('job.title')->label('Job'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('phone'),
            ExportColumn::make('date_of_birth')->label('Date of Birth')
                ->formatStateUsing(fn (?CarbonInterface $state): ?string => $state?->format('Y-m-d')),
            ExportColumn::make('marital_status')->label('Marital Status'),
            ExportColumn::make('current_status')->label('Current Status'),
            ExportColumn::make('current_domicile')->label('Current Domicile'),
            ExportColumn::make('english_fluency')->label('English Fluency'),
            ExportColumn::make('foreign_language_fluency')->label('Foreign Language Fluency'),
            ExportColumn::make('current_salary')->label('Current Salary (IDR)'),
            ExportColumn::make('expected_salary')->label('Expected Salary (IDR)'),
            ExportColumn::make('additional_benefits')->label('Additional Benefits'),
            ExportColumn::make('notice_period')->label('Join Notification'),
            ExportColumn::make('application_source')->label('Application Source'),
            ExportColumn::make('referral_from')->label('Referred By'),
            ExportColumn::make('motivation')->label('Motivation for Apply'),
            ExportColumn::make('reason_for_leaving')->label('Reason of Leaving Last Company'),
            ExportColumn::make('latest_company_reference')->label('Latest Company Reference'),
            ExportColumn::make('second_latest_company_reference')->label('2nd Latest Company Reference'),
            ExportColumn::make('serious_disease')->label('Diagnosed with Serious Disease')
                ->formatStateUsing(fn (?bool $state): string => $state === null ? '' : ($state ? 'Yes' : 'No'))
                ->enabledByDefault(false),
            ExportColumn::make('serious_disease_details')->label('Illness / Disease Details')
                ->enabledByDefault(false),
            ExportColumn::make('status')
                ->formatStateUsing(fn (ApplicantStatus $state): string => $state->label()),
            ExportColumn::make('applied_at')->label('Applied At (WIB)')
                ->formatStateUsing(fn (CarbonInterface $state): string => $state->setTimezone('Asia/Jakarta')->format('Y-m-d H:i')),
            ExportColumn::make('rejection_email_sent_at')->label('Rejection Email Sent At (WIB)')
                ->formatStateUsing(fn (?CarbonInterface $state): ?string => $state?->setTimezone('Asia/Jakarta')->format('Y-m-d H:i')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your applicant export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
