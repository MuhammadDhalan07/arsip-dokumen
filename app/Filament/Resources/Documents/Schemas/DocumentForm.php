<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Enums\JenisRincian;
use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->columns(2)
                            ->native(false)
                            ->preload()
                            ->afterStateUpdated(fn (Set $set, $state) =>
                                $set('title', $state ? \App\Models\Project::find($state)?->name ?? '' : '')
                            )
                            ->live()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('description')
                                    ->label('Deskripsi'),
                                DatePicker::make('start_date')
                                    ->label('Tanggal Mulai')
                                    ->native(false)
                                    ->default(now())
                                    ->required(),
                                DatePicker::make('end_date')
                                    ->label('Tanggal Selesai')
                                    ->native(false)
                                    ->required(),
                            ]),
                        TextInput::make('title')
                            ->label('Title')
                    ]),
                Select::make('rincians')
                    ->label('Rincian')
                    ->relationship('rincians', 'name')
                    ->preload()
                    ->columnSpanFull()
                    ->multiple()
                    ->native(false)
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Rincian Name'),
                        Select::make('type')
                            ->label('Rincian Type')
                            ->options(JenisRincian::class),
                        Toggle::make('is_active')
                            ->label('Is Active')
                            ->default(true),
                    ]),
                Select::make('pic_id')
                    ->label('Penanggung Jawab')
                    ->relationship('pic', 'name')
                    ->preload()
                    ->native(false)
                    ->columnSpanFull()
            ]);
    }
}
