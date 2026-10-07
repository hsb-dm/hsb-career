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
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest applications')
            ->description('Open a profile to review answers, CV, and current status.')
            ->emptyStateHeading('No applications yet')
            ->emptyStateDescription('New applications will appear here when candidates submit a form.')
            ->query(fn (): Builder => Applicant::query()->with('job')->latest('applied_at')->limit(10))
            ->paginated(false)
            ->recordUrl(
                fn (Applicant $record): string => ApplicantResource::getUrl('view', ['record' => $record]),
                shouldOpenInNewTab: true,
            )
            ->columns([
                TextColumn::make('name')->weight('semibold'),
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
