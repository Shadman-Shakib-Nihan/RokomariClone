<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            // Book attributes
            ['name' => 'Pages', 'input_type' => 'number', 'unit' => 'pages'],
            ['name' => 'Language', 'input_type' => 'select', 'unit' => null],
            ['name' => 'ISBN', 'input_type' => 'text', 'unit' => null],
            ['name' => 'Edition', 'input_type' => 'text', 'unit' => null],
            ['name' => 'Cover Type', 'input_type' => 'select', 'unit' => null],

            // Electronics attributes
            ['name' => 'RAM', 'input_type' => 'select', 'unit' => 'GB'],
            ['name' => 'Storage', 'input_type' => 'select', 'unit' => 'GB'],
            ['name' => 'Processor', 'input_type' => 'text', 'unit' => null],
            ['name' => 'Battery', 'input_type' => 'text', 'unit' => 'mAh'],
            ['name' => 'Color', 'input_type' => 'select', 'unit' => null],

            // General attributes
            ['name' => 'Weight', 'input_type' => 'number', 'unit' => 'g'],
            ['name' => 'Warranty', 'input_type' => 'text', 'unit' => null],
        ];

        foreach ($attributes as $attribute) {
            Attribute::firstOrCreate(
                ['slug' => Str::slug($attribute['name'])],
                [
                    'name' => $attribute['name'],
                    'slug' => Str::slug($attribute['name']),
                    'input_type' => $attribute['input_type'],
                    'unit' => $attribute['unit'],
                ]
            );
        }
    }
}
