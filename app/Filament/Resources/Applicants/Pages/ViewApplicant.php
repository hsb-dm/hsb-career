<?php

namespace App\Filament\Resources\Applicants\Pages;

use App\Enums\ApplicantStatus;
use App\Filament\Resources\Applicants\ApplicantResource;
use App\Models\Applicant;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ViewApplicant extends ViewRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateStatus')
                ->label('Update status')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->schema([
                    Select::make('status')
                        ->options(ApplicantStatus::options())
                        ->required(),
                ])
                ->fillForm(fn (Applicant $record): array => [
                    'status' => $record->status->value,
                ])
                ->action(function (Applicant $record, array $data): void {
                    $record->update(['status' => $data['status']]);

                    Notification::make()
                        ->title('Applicant status updated')
                        ->success()
                        ->send();
                }),
            Action::make('downloadCv')
                ->label('Download CV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function (Applicant $record) {
                    if (blank($record->cv_path) || ! Storage::disk('local')->exists($record->cv_path)) {
                        Notification::make()
                            ->title('CV file is not available')
                            ->warning()
                            ->send();

                        return null;
                    }

                    return Storage::disk('local')->download($record->cv_path);
                }),
            Action::make('previewCv')
                ->label('Preview CV')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn (Applicant $record): string => route('admin.applicants.cv.preview', $record))
                ->openUrlInNewTab()
                ->visible(fn (Applicant $record): bool => filled($record->cv_path)
                    && Storage::disk('local')->exists($record->cv_path)
                    && Str::endsWith(strtolower($record->cv_path), '.pdf')),
        ];
    }
}
