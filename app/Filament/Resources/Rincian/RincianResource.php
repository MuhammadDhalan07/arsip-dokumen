<?php

namespace App\Filament\Resources\Rincian;

use App\Enums\JenisRincian;
use App\Filament\Resources\Rincian\Pages\ManageRincian;
use App\Models\Rincian;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use UnitEnum;

class RincianResource extends Resource
{
    protected static ?string $model = Rincian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BarsArrowUp;

    protected static string|UnitEnum|null $navigationGroup = 'Pendukung';

    protected static ?string $recordTitleAttribute = 'Rincian';

    protected static ?string $modelLabel = 'Rincian';

    protected static ?string $pluralModelLabel = 'Rincian';

    protected static ?string $slug = 'pendukung-rincian';

    protected static ?int $navigationSort = 2;


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Dokumen Rincian'),
                        TextInput::make('bobot')
                            ->label('Bobot (%)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Masukkan bobot dalam persen (0-100)'),
                    ]),
                Select::make('type')
                    ->label('Tipe')
                    ->columnSpanFull()
                    ->native(false)
                    ->options(JenisRincian::class),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Placeholder::make('info_bobot')
                    ->label('Info Total Bobot')
                    ->content(function () {
                        $totalBobot = Rincian::where('is_active', true)->sum('bobot');
                        $color = abs($totalBobot - 100) < 0.01 ? 'success' : 'warning';

                        return new \Illuminate\Support\HtmlString(
                            "<div class='text-{$color}-600 font-medium'>
                                Total bobot saat ini: {$totalBobot}%
                                " . ($totalBobot == 100 ? '✓' : '⚠ Harus 100%') . "
                            </div>"
                        );
                    }),
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Rincian')
            ->groups([
                Group::make('type')
                    ->label('Tipe'),
            ])
            ->defaultGroup('type')
            ->recordClasses(fn (Rincian $record) => match ($record->type) {
                JenisRincian::EKSTERNAL => 'bg-blue-50 dark:bg-blue-900',
                JenisRincian::INTERNAL => 'bg-green-50 dark:bg-green-900',
            })
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bobot')
                    ->label('Bobot (%)')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
            'index' => ManageRincian::route('/'),
        ];
    }
}
