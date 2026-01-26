<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateProductExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Contoh data agar user mudah mengisi
        return [
            ['PRD001', 'Laptop ABC', 'Laptop', 'Brand XYZ', 5000000, 6000000],
            ['PRD002', 'Mouse Wireless', 'Aksesoris', 'Brand ABC', 150000, 200000],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Produk',
            'Nama Produk',
            'Kategori',
            'Brand',
            'Harga Beli',
            'Harga Jual',
            'Deskripsi'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
