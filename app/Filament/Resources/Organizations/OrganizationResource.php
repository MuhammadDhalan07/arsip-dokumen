<?php

namespace App\Filament\Resources\Organizations;

use App\Enums\JenisOrganization;
use App\Filament\Resources\Organizations\Pages\ManageOrganizations;
use App\Models\Organization;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static ?string $recordTitleAttribute = 'Organization';

    protected static string|UnitEnum|null $navigationGroup = 'Pendukung';


    protected static ?string $modelLabel = 'Instansi';

    protected static ?string $pluralModelLabel = 'Instansi';

    protected static ?string $slug = 'pendukung-instansi';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('jenis_organization')
                    ->label('Jenis')
                    ->inlineLabel()
                    ->columnSpanFull()
                    ->options(JenisOrganization::class)
                    ->native(false)
                    ->live()
                    ->required(),
                Select::make('id_induk')
                    ->label('Induk SKPD')
                    ->relationship('induk', 'nama_organization', modifyQueryUsing: fn ($query) => $query->whereNull('id_induk'))
                    ->columnSpanFull()
                    ->inlineLabel()
                    ->native(false)
                    ->searchable()
                    ->live()
                    ->visible(fn (Get $get) => $get('jenis_organization') === JenisOrganization::GOVERNMENT)
                    ->preload(),
                TextInput::make('kode_Organization')
                    ->label(fn (Get $get) => 'Kode '.($get('jenis_organization') === JenisOrganization::GOVERNMENT ? 'SKPD' : 'Organization'))
                    ->required(fn (Get $get) => $get('jenis_organization') === JenisOrganization::GOVERNMENT)
                    ->inlineLabel()
                    ->columnSpanFull(),
                TextInput::make('nama_organization')
                    ->label(fn (Get $get) => 'Nama '.($get('jenis_organization') === JenisOrganization::GOVERNMENT ? 'SKPD' : 'Organization'))
                    ->required()
                    ->inlineLabel()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Organization')
            ->columns([
                TextColumn::make('nama_organization')
                    ->label('Instansi')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
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
            'index' => ManageOrganizations::route('/'),
        ];
    }
}
