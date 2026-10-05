<?php

namespace App\Filament\Resources\TalentPool\Pages;

use App\Filament\Resources\TalentPool\TalentPoolResource;
use Filament\Resources\Pages\ListRecords;

class ListTalentPoolEntries extends ListRecords
{
    protected static string $resource = TalentPoolResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
