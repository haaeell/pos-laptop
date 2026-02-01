<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesPerson;

class SalesPeopleSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name'   => 'Andi Pratama',
                'phone'  => '081234567890',
                'active' => true,
            ],
            [
                'name'   => 'Budi Santoso',
                'phone'  => '082345678901',
                'active' => true,
            ],
            [
                'name'   => 'Citra Lestari',
                'phone'  => '083456789012',
                'active' => true,
            ],
            [
                'name'   => 'Dewi Anggraini',
                'phone'  => '084567890123',
                'active' => false,
            ],
        ];

        foreach ($data as $row) {
            SalesPerson::updateOrCreate(
                ['phone' => $row['phone']],
                $row
            );
        }
    }
}
