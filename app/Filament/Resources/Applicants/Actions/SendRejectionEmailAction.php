<?php

namespace App\Filament\Resources\Applicants\Actions;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use RuntimeException;

class SendRejectionEmailAction
{
    public static function make(): Action
    {
        return Action::make('sendRejectionEmail')
            ->label('Send rejection email')
            ->icon('heroicon-o-envelope')
            ->requiresConfirmation()
            ->visible(fn (Applicant $record): bool => $record->status === ApplicantStatus::Rejected
                && filled($record->email)
                && $record->rejection_email_sent_at === null)
            ->action(function (Applicant $record): void {
                try {
                    $sent = $record->sendRejectionEmail();
                } catch (RuntimeException $exception) {
                    report($exception);
                    Notification::make()->title('Rejection email could not be sent')->danger()->send();

                    return;
                }

                if ($sent) {
                    Notification::make()->title('Rejection email sent')->success()->send();
                } else {
                    Notification::make()->title('Rejection email was not sent')->warning()->send();
                }
            });
    }
}
