<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard;

class RecruitmentDashboard extends Dashboard
{
    protected static ?string $title = 'Overview';

    /** @return array<string, int> */
    public function getColumns(): array
    {
        return ['default' => 1, 'xl' => 3];
    }
}
