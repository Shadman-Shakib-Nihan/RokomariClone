<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $publishers = [
            ['name' => 'Anannya', 'description' => 'Leading Bangladeshi publisher of Bengali literature.'],
            ['name' => 'Prothoma', 'description' => 'Renowned Bangladeshi publishing house.'],
            ['name' => 'Adarsha', 'description' => 'Well-known Bangladeshi book publisher.'],
            ['name' => 'Penguin Random House', 'description' => 'Global trade book publisher headquartered in New York.'],
            ['name' => "O'Reilly Media", 'description' => 'American learning company for technology knowledge.'],
            ['name' => 'Packt Publishing', 'description' => 'UK-based publisher of technology books and videos.'],
        ];

        foreach ($publishers as $publisher) {
            Publisher::firstOrCreate(
                ['slug' => Str::slug($publisher['name'])],
                [
                    'name' => $publisher['name'],
                    'slug' => Str::slug($publisher['name']),
                    'description' => $publisher['description'],
                    'is_active' => true,
                ]
            );
        }
    }
}
