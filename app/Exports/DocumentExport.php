<?php

namespace App\Exports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromCollection;

class DocumentExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function make()
    {
        
    }
    public function collection()
    {
        return Document::all();
    }
}
