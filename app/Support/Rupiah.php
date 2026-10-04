<?php

namespace App\Support;

class Rupiah
{
    public static function format(?int $amount): string
    {
        if ($amount === null) {
            return '-';
        }

        return 'Rp'.number_format($amount, 0, ',', '.');
    }
}
