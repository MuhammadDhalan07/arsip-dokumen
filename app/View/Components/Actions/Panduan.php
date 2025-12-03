<?php

namespace App\View\Components\Actions;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Panduan extends Component
{
    public $record;
    public $rincian;

    public function __construct($record, $rincian)
    {
        $this->record = $record;
        $this->rincian = $rincian;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.actions.panduan');
    }
}
