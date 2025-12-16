<?php

namespace App\Filament\Resources\Projects\Schemas;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('year'),
                TextInput::make('name')
                    ->label('Nama Proyek')
                    ->columnSpanFull()
                    ->required(),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('year', Carbon::parse($state)->year))
                    ->live()
                    ->displayFormat('d F Y')
                    ->native(false)
                    ->columns(2)
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->displayFormat('d F Y')
                    ->native(false)
                    ->columns(2),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
