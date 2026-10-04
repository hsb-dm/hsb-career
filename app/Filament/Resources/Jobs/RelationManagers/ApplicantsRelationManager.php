<?php

namespace App\Filament\Resources\Jobs\RelationManagers;

use App\Filament\Resources\Applicants\Schemas\ApplicantForm;
use App\Filament\Resources\Applicants\Tables\ApplicantsTable;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ApplicantsRelationManager extends RelationManager
{
    protected static string $relationship = 'applicants';

    public function form(Schema $schema): Schema
    {
        return ApplicantForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ApplicantsTable::configure($table)
            ->headerActions([])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
