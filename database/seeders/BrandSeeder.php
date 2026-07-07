<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple', 'description' => 'Premium consumer electronics and software.'],
            ['name' => 'Samsung', 'description' => 'South Korean electronics and appliances giant.'],
            ['name' => 'Xiaomi', 'description' => 'Chinese electronics company known for smartphones and smart devices.'],
            ['name' => 'Anker', 'description' => 'Leading charging technology and accessories brand.'],
            ['name' => 'Logitech', 'description' => 'Swiss manufacturer of computer peripherals and software.'],
            ['name' => 'HP', 'description' => 'American IT company known for printers and personal computers.'],
            ['name' => 'Dell', 'description' => 'American computer technology company.'],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['slug' => Str::slug($brand['name'])],
                [
                    'name' => $brand['name'],
                    'slug' => Str::slug($brand['name']),
                    'description' => $brand['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
