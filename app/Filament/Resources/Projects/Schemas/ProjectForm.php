<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\JenisOrganization;
use App\Models\Tax;
use Carbon\Carbon;
use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
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
                Select::make('organization_id')
                    ->label('Instansi')
                    ->relationship('organizations', 'nama_organization')
                    ->native(false)
                    ->columnSpanFull()
                    ->preload()
                    ->required()
                    ->createOptionForm([
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
                    ]),
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
                Section::make('')
                    ->columns(8)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nilai_kontrak')
                            ->label('Nilai Kontrak')
                            ->numeric(),
                        TextInput::make('nilai_dpp')
                            ->label('DPP')
                            ->numeric(),
                        TextInput::make('nilai_ppn')
                            ->label('PPN')
                            ->numeric()
                            ->suffix('%'),
                        TextInput::make('nilai_pph')
                            ->label('PPH')
                            ->numeric()
                            ->default(fn () => Tax::first()->pph ?? 0)
                            ->suffix('%'),
                        TextInput::make('billing_ppn')
                            ->label('Billing PPN'),
                        TextInput::make('billing_pph')
                            ->label('Billing PPH'),
                        TextInput::make('ntpn_ppn')
                            ->label('NTPN PPN'),
                        TextInput::make('ntpn_pph')
                            ->label('NTPN PPH'),

                    ]),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
