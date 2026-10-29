<?php

namespace App\Filament\Exports;

use App\Models\TalentPoolEntry;
use Carbon\CarbonInterface;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class TalentPoolEntryExporter extends Exporter
{
    protected static ?string $model = TalentPoolEntry::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('area_of_interest')->label('Area of Interest'),
            ExportColumn::make('status')
                ->formatStateUsing(fn (?string $state): string => TalentPoolEntry::STATUSES[$state] ?? 'Not set'),
            ExportColumn::make('created_at')->label('Submitted At (WIB)')
                ->formatStateUsing(fn (CarbonInterface $state): string => $state->setTimezone('Asia/Jakarta')->format('Y-m-d H:i')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your talent pool export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
