<?php

namespace App\Filament\Resources\Applicants\Actions;

use App\Models\Applicant;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicantCvActions
{
    public static function preview(): Action
    {
        return Action::make('previewCv')
            ->label('Preview CV')
            ->icon('heroicon-o-eye')
            ->color('gray')
            ->url(fn (Applicant $record): string => route('admin.applicants.cv.preview', $record))
            ->openUrlInNewTab()
            ->visible(fn (Applicant $record): bool => self::hasFile($record)
                && Str::endsWith(strtolower($record->cv_path), '.pdf'));
    }

    public static function download(): Action
    {
        return Action::make('downloadCv')
            ->label('Download CV')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->url(fn (Applicant $record): string => route('admin.applicants.cv.download', $record))
            ->visible(fn (Applicant $record): bool => self::hasFile($record));
    }

    private static function hasFile(Applicant $record): bool
    {
        return filled($record->cv_path) && Storage::disk('local')->exists($record->cv_path);
    }
}
