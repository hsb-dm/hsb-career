<?php

namespace App\Enums;

enum ApplicantStatus: string
{
    case AiAtsScreened = 'ai_ats_screened';
    case HrInterview = 'hr_interview';
    case ForwardedToUser = 'forwarded_to_user';
    case UserInterview = 'user_interview';
    case Hired = 'hired';
    case Rejected = 'rejected';
    case OnHold = 'on_hold';
    case NoShow = 'no_show';
    case WithdrawDecline = 'withdraw_decline';
    case StudyCase = 'study_case';

    public function label(): string
    {
        return match ($this) {
            self::AiAtsScreened => 'AI ATS Screened',
            self::HrInterview => 'HR Interview',
            self::ForwardedToUser => 'Forwarded to User',
            self::UserInterview => 'User Interview',
            self::Hired => 'Hired',
            self::Rejected => 'Rejected',
            self::OnHold => 'On Hold',
            self::NoShow => 'No show',
            self::WithdrawDecline => 'Withdraw/Decline',
            self::StudyCase => 'Study Case',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AiAtsScreened => 'gray',
            self::HrInterview, self::ForwardedToUser, self::StudyCase => 'info',
            self::UserInterview, self::OnHold => 'warning',
            self::Hired => 'success',
            self::Rejected, self::NoShow, self::WithdrawDecline => 'danger',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->all();
    }
}
