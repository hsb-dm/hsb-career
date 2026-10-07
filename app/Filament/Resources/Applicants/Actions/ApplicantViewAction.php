<?php

namespace App\Filament\Resources\Applicants\Actions;

use App\Filament\Resources\Applicants\ApplicantResource;
use App\Models\Applicant;
use Filament\Actions\ViewAction;

class ApplicantViewAction
{
    public static function make(): ViewAction
    {
        return ViewAction::make()
            ->url(fn (Applicant $record): string => ApplicantResource::getUrl('view', ['record' => $record]))
            ->openUrlInNewTab();
    }
}
