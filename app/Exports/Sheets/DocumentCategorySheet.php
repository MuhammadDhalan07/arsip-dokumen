<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DocumentCategorySheet implements FromCollection, WithColumnFormatting, WithColumnWidths, WithHeadings, WithStyles, WithTitle
{
    protected string $category;

    protected Collection $documents;

    protected int $maxRowData = 0;

    protected string $lastColumn = 'N';

    public function __construct(string $category, Collection $documents)
    {
        $this->category = $category;
        $this->documents = $documents;
        $this->maxRowData = $documents->count() + 1;
    }

    public function title(): string
    {
        $friendlyName = $this->getFriendlyName($this->category);
        $sanitized = preg_replace('/[^a-zA-Z0-9 ]/', '', $friendlyName);
        $sanitized = trim($sanitized);

        return empty($sanitized) ? 'Sheet' : substr($sanitized, 0, 31);
    }

    private function getFriendlyName(string $category): string
    {
        try {
            $enum = \App\Enums\JenisProject::from($category);

            return $enum->getLabel();
        } catch (\ValueError $e) {
            if ($category === 'uncategorized') {
                return 'Tidak Ada Kategori';
            }

            return ucwords(str_replace('_', ' ', $category));
        }
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Proyek',
            'Instansi',
            'Nilai Kontrak',
            'DPP',
            'Rate PPN %',
            'Nilai PPN',
            'Rate PPH %',
            'Nilai PPH',
            'Billing PPN',
            'Billing PPH',
            'NTPN PPN',
            'NTPN PPH',
            'Deskripsi',
        ];
    }

    public function collection()
    {
        $records = collect();

        $totalKontrak = 0;
        $totalDPP = 0;
        $totalPPN = 0;
        $totalPPH = 0;

        foreach ($this->documents as $index => $doc) {
            $project = $doc->project;

            $totalKontrak += $project?->nilai_kontrak ?? 0;
            $totalDPP += $project?->nilai_dpp ?? 0;
            $totalPPN += $project?->nilai_ppn ?? 0;
            $totalPPH += $project?->nilai_pph ?? 0;

            $namaOrganization = $project?->organizations?->nama_organization ?? '-';

            $records->push([
                $index + 1,
                $project?->name ?? '-',
                $namaOrganization,
                $project?->nilai_kontrak ?? 0,
                $project?->nilai_dpp ?? 0,
                ($project?->ppn ?? 0),
                $project?->nilai_ppn ?? 0,
                ($project?->pph ?? 0),
                $project?->nilai_pph ?? 0,
                $project?->billing_ppn ?? '-',
                $project?->billing_pph ?? '-',
                $project?->ntpn_ppn ?? '-',
                $project?->ntpn_pph ?? '-',
                $doc->description ?? '-',
            ]);
        }

        $records->push([
            '',
            'TOTAL',
            '',
            $totalKontrak,
            $totalDPP,
            '',
            $totalPPN,
            '',
            $totalPPH,
            '', '', '', '', '',
        ]);
        $this->maxRowData = $records->count() + 1;

        return $records;
    }

    public function styles(Worksheet $sheet)
    {
        // $maxRowData = $this->maxRowData;
        $lastRow = $this->maxRowData;
        $lastColumn = $this->lastColumn;
        $totalRow = $lastRow;

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFD700'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            "A1:{$lastColumn}{$totalRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
            "A{$totalRow}:{$lastColumn}{$totalRow}" => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFD700'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 30,
            'D' => 30,
            'E' => 30,
            'F' => 18,
            'G' => 30,
            'H' => 18,
            'I' => 30,
            'J' => 30,
            'K' => 30,
            'L' => 30,
            'M' => 30,
            'N' => 30,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'E' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'G' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'I' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
        ];
    }

    public function maxRows(): int
    {
        return $this->documents->count();
    }

    public function getMaxrowData(): ?int
    {
        return $this->maxRowData;
    }
}
