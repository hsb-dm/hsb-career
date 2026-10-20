<?php

namespace App\Filament\Resources\Applicants\Pages;

use App\Enums\ApplicantStatus;
use App\Filament\Resources\Applicants\Actions\ApplicantCvActions;
use App\Filament\Resources\Applicants\Actions\UpdateStatusAction;
use App\Filament\Resources\Applicants\ApplicantResource;
use App\Models\Applicant;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewApplicant extends ViewRecord
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            UpdateStatusAction::make(),
            ApplicantCvActions::preview(),
            ApplicantCvActions::download(),
            Action::make('sendRejectionEmail')
                ->label('Send rejection email')
                ->requiresConfirmation()
                ->visible(fn (Applicant $record): bool => $record->status === ApplicantStatus::Rejected
                    && filled($record->email)
                    && $record->rejection_email_sent_at === null)
                ->action(function (Applicant $record): void {
                    $record->sendRejectionEmail();
                    Notification::make()->title('Rejection email sent')->success()->send();
                }),
            DeleteAction::make(),
        ];
    }
}
