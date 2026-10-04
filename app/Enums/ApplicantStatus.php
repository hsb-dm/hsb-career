<?php

namespace App\Enums;

enum ApplicantStatus: string
{
    case New = 'new';
    case Reviewing = 'reviewing';
    case Interview = 'interview';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::New => 'New',
            self::Reviewing => 'Reviewing',
            self::Interview => 'Interview',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::New => 'gray',
            self::Reviewing => 'info',
            self::Interview => 'warning',
            self::Accepted => 'success',
            self::Rejected => 'danger',
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
