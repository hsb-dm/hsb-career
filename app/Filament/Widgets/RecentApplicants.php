<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicantStatus;
use App\Filament\Resources\Applicants\ApplicantResource;
use App\Models\Applicant;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentApplicants extends TableWidget
{
    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Recent applicants')
            ->query(fn (): Builder => Applicant::query()->with('job')->latest('applied_at')->limit(10))
            ->paginated(false)
            ->recordUrl(fn (Applicant $record): string => ApplicantResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('job.title')
                    ->label('Job'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ApplicantStatus $state): string => $state->label())
                    ->color(fn (ApplicantStatus $state): string => $state->color()),
                TextColumn::make('applied_at')
                    ->label('Applied at')
                    ->dateTime('d M Y H:i'),
            ]);
    }
}
