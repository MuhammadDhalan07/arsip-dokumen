<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class DocumentCategorySheet implements FromCollection, WithTitle, WithStyles, WithColumnWidths, WithColumnFormatting, WithHeadings
{
    protected string $category;
    protected Collection $documents;

    public function __construct(string $category, Collection $documents)
    {
        $this->category = $category;
        $this->documents = $documents;
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
        $mapping = [
            'konsultan' => 'Konsultan',
            'pengadaan_barang' => 'Pengadaan Barang',
            'pengadaan_jasa' => 'Pengadaan Jasa',
            'konstruksi' => 'Konstruksi',
            'pekerjaan_konstruksi' => 'Pekerjaan Konstruksi',
            'jasa_konsultansi' => 'Jasa Konsultansi',
            'jasa_lainnya' => 'Jasa Lainnya',
            'uncategorized' => 'Tidak Ada Kategori',
        ];

        return $mapping[$category] ?? ucwords(str_replace('_', ' ', $category));
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Proyek',
            'Instansi',
            'Nilai Kontrak',
            'DPP',
            'Rate PPN',
            'Nilai PPN',
            'Rate PPH',
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

        foreach ($this->documents as $index => $doc) {
            $project = $doc->project;

            $records->push([
                $index + 1,
                $project?->name ?? '-',
                $project->organizations->nama_organization ?? '-',
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

            // $records->push([
            //     $index + 1,
            //     $doc->project->name,
            //     $doc->project->organizations->nama_organization,
            //     $doc->project->nilai_kontrak,
            //     $doc->project->nilai_dpp,
            //     $doc->project->ppn,
            //     $doc->project->nilai_ppn,
            //     $doc->project->pph,
            //     $doc->project->nilai_pph,
            //     $doc->project->billing_ppn,
            //     $doc->project->billing_pph,
            //     $doc->project->ntpn_ppn,
            //     $doc->project->ntpn_pph,
            //     $doc->description,
            // ]);
        }

        return $records;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->documents->count() + 1;

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            "2:{$lastRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ],
            'I:I' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF2CC']
                ],
            ],
            'K:K' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FCE4D6']
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
            'D' => 35,
            'E' => 15,
            'F' => 10,
            'G' => 18,
            'H' => 18,
            'I' => 12,
            'J' => 18,
            'K' => 12,
            'L' => 18,
            'M' => 20,
            'N' => 20,
            'O' => 20,
            'P' => 20,
            'Q' => 40,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'H' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'J' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
            'L' => '_("Rp"* #,##0_);_("Rp"* (#,##0);_("Rp"* "-"_);_(@_)',
        ];
    }
}
