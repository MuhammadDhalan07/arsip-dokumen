<?php

namespace App\Filament\Resources\Documents\Schemas;

use App\Enums\JenisRincian;
use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class DocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('created_by')
                    ->default(Auth::user()->id)
                    ->dehydrated(),
                Hidden::make('year')
                    ->dehydrated(),
                Section::make('')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('project_id')
                            ->label('Proyek')
                            ->relationship('project', 'name')
                            ->columns(2)
                            ->native(false)
                            ->preload()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $project = \App\Models\Project::select('id', 'start_date')
                                        ->find($state);

                                    if ($project && $project->start_date) {
                                        $set('year', $project->start_date->format('Y'));
                                    }
                                }
                            })
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

                    ]),
                Select::make('pic_id')
                    ->label('Penanggung Jawab')
                    ->relationship('pic', 'name')
                    ->preload()
                    ->columnSpanFull()
                    ->native(false),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->columnSpanFull(),
            ]);
    }
}
