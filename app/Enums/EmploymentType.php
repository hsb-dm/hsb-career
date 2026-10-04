<?php

namespace App\Enums;

enum EmploymentType: string
{
    case FullTime = 'full_time';
    case Contract = 'contract';
    case Internship = 'internship';
    case Freelance = 'freelance';

    public function label(): string
    {
        return match ($this) {
            self::FullTime => 'Full Time',
            self::Contract => 'Contract',
            self::Internship => 'Internship',
            self::Freelance => 'Freelance',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->all();
    }
}
