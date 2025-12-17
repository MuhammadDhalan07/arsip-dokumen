<?php

namespace App\Filament\Pages;

use App\Models\Tax as ModelsTax;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Tax extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static ?string $model = ModelsTax::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Pendukung';

    protected static ?string $recordTitleAttribute = 'Pajak';

    protected static ?string $modelLabel = 'Pajak';
    
    protected static ?string $title = 'Pajak';

    protected static ?string $pluralModelLabel = 'Pajak';

    protected static ?string $slug = 'pendukung-pajak';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.tax';

    public ?array $data = [];

    public function mount(): void
    {
        $tax = ModelsTax::first();

        $this->form->fill([
            'pph' => $tax?->pph ?? 0,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('')
                    ->maxWidth('md')
                    ->tabs([
                        Tabs\Tab::make('PPH')
                            ->schema([
                                TextInput::make('pph')
                                    ->disableLabel()
                                    ->numeric()
                                    ->suffix('%')
                                    ->maxWidth('sm')
                        ])
                    ])
            ])->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();

        try {
            $tax = ModelsTax::first();

            if ($tax) {
                $tax->update($data);
            } else {
                ModelsTax::create($data);
            }

            $this->mount();

            Notification::make()
                ->title('Berhasil!')
                ->body('Data pajak berhasil disimpan.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal!')
                ->body('Terjadi kesalahan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
