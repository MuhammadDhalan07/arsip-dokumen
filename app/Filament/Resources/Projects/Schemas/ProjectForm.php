<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\JenisOrganization;
use App\Enums\JenisProject;
use Carbon\Carbon;
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
                Select::make('type')
                    ->label('Tipe Proyek')
                    ->options(JenisProject::class)
                    ->native(false)
                    ->columnSpanFull()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, JenisProject|string|null $state) {
                        if ($state) {
                            $jenis = $state instanceof JenisProject ? $state : JenisProject::from($state);
                            $set('pph', $jenis->getPph());
                            if (! $jenis->hasPpn()) {
                                $set('ppn', 0);
                            }
                            self::updateCalculations($set, $get);
                        }
                    }),
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
                    ->columns(10)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nilai_dpp')
                            ->label('DPP')
                            ->prefix('Rp')
                            ->columnSpan(2)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCalculations($set, $get))
                            ->numeric()
                            ->stripCharacters(',')
                            ->required()
                            ->placeholder('1000000')
                            ->helperText('Ketik angka saja, tanpa titik atau koma.'),

                        TextInput::make('ppn')
                            ->label('PPN')
                            ->maxWidth('sm')
                            ->numeric()
                            ->live(onBlur: true)
                            ->hidden(fn (Get $get) => ($get('type') instanceof JenisProject && ! $get('type')->hasPpn()) || $get('type') === 'perorangan')
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCalculations($set, $get))
                            ->suffix('%'),

                        TextInput::make('nilai_ppn')
                            ->label('Nilai PPN')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(2)
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : '0')
                            ->prefix('Rp')
                            ->hidden(fn (Get $get) => ($get('type') instanceof JenisProject && ! $get('type')->hasPpn()) || $get('type') === 'perorangan'),

                        TextInput::make('pph')
                            ->label('PPH')
                            ->maxWidth('sm')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => self::updateCalculations($set, $get))
                            ->numeric()
                            ->suffix('%'),

                        TextInput::make('nilai_pph')
                            ->label('Nilai PPH')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(2)
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : '0')
                            ->prefix('Rp'),
                    ]),
                Section::make('')
                    ->columns(10)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('nilai_kontrak')
                            ->label('Nilai Kontrak')
                            ->prefix('Rp')
                            ->columnSpan(3)
                            ->disabled()
                            ->dehydrated()
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : '0'),

                        TextInput::make('netto')
                            ->label('Netto')
                            ->prefix('Rp')
                            ->columnSpan(3)
                            ->disabled()
                            ->dehydrated()
                            ->formatStateUsing(fn ($state) => $state ? number_format($state, 0, ',', '.') : '0'),
                    ]),
                Section::make('')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('billing_ppn')
                            ->label('Billing PPN'),
                        TextInput::make('billing_pph')
                            ->label('Billing PPH'),
                        TextInput::make('ntpn_ppn')
                            ->label('NTPN PPN'),
                        TextInput::make('ntpn_pph')
                            ->label('NTPN PPH'),

                    ]),
                Select::make('projectContributors')
                    ->label('Kontributor')
                    ->relationship('projectContributors', 'name')
                    ->multiple()
                    ->preload()
                    ->columnSpanFull()
                    ->searchable(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull(),
            ]);
    }

    protected static function updateCalculations(Set $set, Get $get): void
    {
        $dpp = (float) ($get('nilai_dpp') ?? 0);
        $ppnRate = (float) ($get('ppn') ?? 0);
        $pphRate = (float) ($get('pph') ?? 0);

        // DPP × PPN% = Nilai PPN
        $nilaiPpn = $dpp * ($ppnRate / 100);

        // DPP + PPN = Nilai Kontrak
        $nilaiKontrak = $dpp + $nilaiPpn;

        // DPP × PPH% = Nilai PPH
        $nilaiPph = $dpp * ($pphRate / 100);

        // Nilai Kontrak - PPN - PPH = Netto
        $netto = $nilaiKontrak - $nilaiPpn - $nilaiPph;

        $set('nilai_ppn', round($nilaiPpn, 2));
        $set('nilai_kontrak', round($nilaiKontrak, 2));
        $set('nilai_pph', round($nilaiPph, 2));
        $set('netto', round($netto, 2));
    }
}
