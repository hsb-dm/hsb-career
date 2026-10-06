<?php

namespace App\Filament\Resources\Applicants\Actions;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class UpdateStatusAction
{
    public static function make(): Action
    {
        return Action::make('updateStatus')
            ->label('Update status')
            ->icon('heroicon-o-pencil-square')
            ->color('primary')
            ->schema([
                Select::make('status')
                    ->label('Status')
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
            });
    }
}
