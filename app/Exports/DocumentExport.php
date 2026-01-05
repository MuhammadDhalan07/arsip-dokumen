<?php

namespace App\Exports;

use App\Exports\Sheets\DocumentCategorySheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Style\Style;

class DocumentExport implements WithMultipleSheets, WithDefaultStyles
{
    use Exportable;

    protected Collection $documents;

    public static function make(Collection $documents)
    {
        $name = Str::of('rekap-' . now()->format('Y-m-d-His'))->append('.xlsx');
        $format = \Maatwebsite\Excel\Excel::XLSX;

        return app(static::class)->setUp($documents)->download($name, $format);
    }

    public function setUp(Collection $documents): self
    {
        $this->documents = $documents;
        return $this;
    }

    public function sheets(): array
    {
        $sheets = [];

        $grouped = $this->documents->groupBy(function ($item) {
            return $item->project?->type?->value;
        });

        foreach ($grouped as $category => $documents) {
            $sheets[] = new DocumentCategorySheet($category, $documents);
        }

        return $sheets;
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'wrapText' => true,
            ],
            'font' => [
                'name' => 'Times New Roman',
                'size' => 12,
            ],
        ];
    }
}
