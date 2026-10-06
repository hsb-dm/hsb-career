<?php

namespace App\Filament\Resources\Applicants\Tables;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use App\Support\Rupiah;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ApplicantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('job'))
            ->defaultSort('applied_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('job.title')
                    ->label('Job')
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('current_salary')
                    ->label('Current salary')
                    ->state(fn (Applicant $record): string => $record->current_salary_answer ?? Rupiah::format($record->current_salary))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('expected_salary')
                    ->label('Expected salary')
                    ->state(fn (Applicant $record): string => $record->expected_salary_answer ?? Rupiah::format($record->expected_salary))
                    ->sortable(),
                TextColumn::make('notice_period')
                    ->label('Notice period')
                    ->toggleable(),
                SelectColumn::make('status')
                    ->label('Status')
                    ->options(ApplicantStatus::options())
                    ->selectablePlaceholder(false),
                TextColumn::make('applied_at')
                    ->label('Applied at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('job_id')
                    ->label('Job')
                    ->relationship('job', 'title')
                    ->searchable(),
                SelectFilter::make('status')
                    ->options(ApplicantStatus::options()),
                Filter::make('applied_at')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Applied from'),
                        DatePicker::make('until')
                            ->label('Applied until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('applied_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, string $date) => $query->whereDate('applied_at', '<=', $date))),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('updateStatus')
                        ->label('Update status')
                        ->schema([
                            Select::make('status')
                                ->options(ApplicantStatus::options())
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            Applicant::query()
                                ->whereKey($records->pluck('id'))
                                ->update(['status' => $data['status']]);

                            Notification::make()
                                ->title('Applicant statuses updated')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }
}
