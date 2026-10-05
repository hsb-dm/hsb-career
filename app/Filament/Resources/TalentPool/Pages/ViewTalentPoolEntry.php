<?php

namespace App\Filament\Resources\TalentPool\Pages;

use App\Filament\Resources\TalentPool\TalentPoolResource;
use App\Models\TalentPoolEntry;
use Filament\Actions\Action;
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
            Action::make('downloadCv')
                ->label('Download CV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (TalentPoolEntry $record) {
                    if (! Storage::disk('local')->exists($record->cv_path)) {
                        Notification::make()->title('CV file is not available')->warning()->send();

                        return null;
                    }

                    return Storage::disk('local')->download($record->cv_path, basename($record->cv_path));
                }),
            Action::make('previewCv')
                ->label('Preview CV')
                ->icon('heroicon-o-eye')
                ->url(fn (TalentPoolEntry $record): string => route('admin.talent-pool.cv.preview', $record))
                ->openUrlInNewTab()
                ->visible(fn (TalentPoolEntry $record): bool => Storage::disk('local')->exists($record->cv_path)
                    && Str::endsWith(strtolower($record->cv_path), '.pdf')),
        ];
    }
}
