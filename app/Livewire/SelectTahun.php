<?php

namespace App\Livewire;

use App\Models\Tahun;
use Filament\Forms\Components\Select;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class SelectTahun extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public $tahun;

    public $tahunOptions = [];


    public function mount()
    {
        $this->tahunOptions = Tahun::where('is_active', 1)
            ->pluck('year', 'year')
            ->toArray();

        $this->tahun = Session::get('tahun-aktif', !empty($this->tahunOptions) ? max($this->tahunOptions) : null);

        // if (!Session::has('tahun-aktif')) {
        //     Session::put('tahun-aktif', $this->tahun);
        // }

        $this->form->fill([
            // 'year' => array_search($this->tahun, $this->tahunOptions),
            'year' => $this->tahun,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath(path: 'tahun')
            ->components([
                Select::make('year')
                    ->label('Pilih Tahun')
                    ->options($this->tahunOptions)
                    ->inlineLabel()
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(fn ($state) => $this->updatedYear($state))
                    ->native(false)
                    ->live()
                    ->extraAttributes(['class' => 'flex-row items-center gap-2']),
            ]);
    }

    public function updatedYear($value)
    {
        // dd($value);
        Session::put('tahun-aktif', $value);

        return redirect(request()->header('Referer'));
    }
    public function render()
    {
        return view('livewire.select-tahun');
    }
}
