<?php

namespace App\Filament\Resources\Tahun;

use App\Filament\Resources\Tahun\Pages\ManageTahun;
use App\Models\Tahun;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class TahunResource extends Resource
{
    protected static ?string $model = Tahun::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $recordTitleAttribute = 'Tahun';

    protected static ?string $modelLabel = 'Tahun';

    protected static ?string $pluralModelLabel = 'Tahun';

    protected static ?string $slug = 'pngaturan-tahun';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Tahun')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Tahun')
            ->columns([
                TextColumn::make('Tahun')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTahun::route('/'),
        ];
    }
}
