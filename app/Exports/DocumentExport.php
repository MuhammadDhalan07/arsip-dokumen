<?php

namespace App\Exports;

use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;

class DocumentExport implements FromCollection, WithStyles, WithColumnWidths, WithColumnFormatting
{
    use Exportable;

    protected int $maxRowData;

    protected Collection $document;

    protected Collection $records;
    /**
    * @return \Illuminate\Support\Collection
    */

    public static function make(Collection $document)
    {
        $name = Str::of('rekap')->append('.xlsx');
        $format = \Maatwebsite\Excel\Excel::XLSX;

        return app(static::class)->setUp($document)->download($name, $format);
    }

    public function setUp(Collection $document): self
    {
        $this->document = $document;
        $this->records = collect();
        // $this->maxRowData = $document->count() + 1;



        // dd($document);
        foreach ($document as $index => $doc) {
            $this->records->push([
                ['Rekapitulasi Dokumen'],
                [],
                [
                    'No',
                    'Nama Proyek',
                    'Instansi',
                    'Nilai Kontrak',
                    'DPP',
                    'PPN ('. $doc->project->ppn . '%)',
                    'PPH ('. $doc->project->pph . '%)',
                    'Billing PPN',
                    'Billing PPH',
                    'NTPN PPN',
                    'NTPN PPH',
                    'Deskripsi',
                ]
            ]);
            $this->records->push([
                $index + 1,
                $doc->project->name,
                $doc->project->organizations->nama_organization,
                $doc->project->nilai_kontrak,
                $doc->project->nilai_dpp,
                $doc->project->nilai_ppn,
                $doc->project->nilai_pph,
                $doc->project->billing_ppn,
                $doc->project->billing_pph,
                $doc->project->ntpn_ppn,
                $doc->project->ntpn_pph,
                $doc->description,
            ]);
        }

        return $this;
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
    {
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 50,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 20,
            'K' => 50,
            'L' => 20,
            'M' => 20,
            'N' => 50,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'E' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'F' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'G' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
        ];
    }


    public function collection()
    {
        return $this->records;
    }
}
