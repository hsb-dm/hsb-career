<?php

namespace App\Filament\Resources\Applicants\Pages;

use App\Filament\Resources\Applicants\Actions\ApplicantCvActions;
use App\Filament\Resources\Applicants\Actions\UpdateStatusAction;
use App\Filament\Resources\Applicants\ApplicantResource;
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
        ];
    }
}
