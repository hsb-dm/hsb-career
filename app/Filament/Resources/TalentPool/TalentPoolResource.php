<?php

namespace App\Filament\Resources\TalentPool;

use App\Filament\Resources\TalentPool\Pages\ListTalentPoolEntries;
use App\Filament\Resources\TalentPool\Pages\ViewTalentPoolEntry;
use App\Models\TalentPoolEntry;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TalentPoolResource extends Resource
{
    protected static ?string $model = TalentPoolEntry::class;

    protected static ?string $navigationLabel = 'Talent Pool';

    protected static ?string $modelLabel = 'Talent Pool Entry';

    protected static ?string $pluralModelLabel = 'Talent Pool';

    protected static ?string $slug = 'talent-pool';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|UnitEnum|null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('area_of_interest')->label('Area of interest')->searchable()->sortable(),
                TextColumn::make('created_at')->label('Submitted at')->dateTime('d M Y H:i')->sortable(),
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Talent Pool submission')->schema([
                TextEntry::make('name'),
                TextEntry::make('email'),
                TextEntry::make('area_of_interest')->label('Area of interest'),
                TextEntry::make('created_at')->label('Submitted at')->dateTime('d M Y H:i'),
                TextEntry::make('cv_path')->label('CV')->formatStateUsing(fn (?string $state): string => $state ? basename($state) : '-'),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTalentPoolEntries::route('/'),
            'view' => ViewTalentPoolEntry::route('/{record}'),
        ];
    }
}
