<?php

namespace App\Filament\Resources\TalentPool\Pages;

use App\Filament\Resources\TalentPool\TalentPoolResource;
use App\Models\TalentPoolEntry;
use App\Support\CvStorage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ViewTalentPoolEntry extends ViewRecord
{
    protected static string $resource = TalentPoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            Action::make('downloadCv')
                ->label('Download CV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (TalentPoolEntry $record) {
                    $disk = CvStorage::readDisk($record->cv_path);
                    if ($disk === null) {
                        Notification::make()->title('CV file is not available')->warning()->send();

                        return null;
                    }

                    return Storage::disk($disk)->download($record->cv_path, basename($record->cv_path));
                }),
            Action::make('previewCv')
                ->label('Preview CV')
                ->icon('heroicon-o-eye')
                ->url(fn (TalentPoolEntry $record): string => route('admin.talent-pool.cv.preview', $record))
                ->openUrlInNewTab()
                ->visible(fn (TalentPoolEntry $record): bool => CvStorage::readDisk($record->cv_path) !== null
                    && Str::endsWith(strtolower($record->cv_path), '.pdf')),
        ];
    }
}
