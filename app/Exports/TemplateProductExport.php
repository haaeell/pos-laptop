<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplateProductExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new MainSheet(),
            new ReferenceSheet(),
        ];
    }
}

class MainSheet implements WithHeadings, WithTitle {
    public function headings(): array {
        return ['kode_produk', 'nama_produk', 'kategori', 'brand', 'harga_beli', 'harga_jual'];
    }
    public function title(): string { return 'Isi Data Produk'; }
}

class ReferenceSheet implements FromCollection, WithHeadings, WithTitle {
    public function collection() {
        $categories = Category::pluck('name')->toArray();
        $brands = Brand::pluck('name')->toArray();

        $max = max(count($categories), count($brands));
        $data = [];
        for ($i = 0; $i < $max; $i++) {
            $data[] = [
                'kategori' => $categories[$i] ?? '',
                'brand' => $brands[$i] ?? ''
            ];
        }
        return collect($data);
    }
    public function headings(): array { return ['Daftar Kategori Tersedia', 'Daftar Brand Tersedia']; }
    public function title(): string { return 'Panduan Nama'; }
}
