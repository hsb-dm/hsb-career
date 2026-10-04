<?php

namespace App\Enums;

enum WorkArrangement: string
{
    case Onsite = 'onsite';
    case Hybrid = 'hybrid';
    case Remote = 'remote';

    public function label(): string
    {
        return match ($this) {
            self::Onsite => 'Onsite',
            self::Hybrid => 'Hybrid',
            self::Remote => 'Remote',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $arrangement) => [$arrangement->value => $arrangement->label()])
            ->all();
    }
}
