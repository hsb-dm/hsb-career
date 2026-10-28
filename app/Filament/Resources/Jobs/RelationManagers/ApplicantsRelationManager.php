<?php

namespace App\Filament\Resources\Jobs\RelationManagers;

use App\Filament\Resources\Applicants\Actions\ApplicantViewAction;
use App\Filament\Resources\Applicants\Tables\ApplicantsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ApplicantsRelationManager extends RelationManager
{
    protected static string $relationship = 'applicants';

    public function table(Table $table): Table
    {
        return ApplicantsTable::configure($table)
            ->headerActions([])
            ->recordActions([
                ApplicantViewAction::make(),
            ]);
    }
}
