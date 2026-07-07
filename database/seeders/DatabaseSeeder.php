<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Phase 1 — Master Data
            CategorySeeder::class,
            BrandSeeder::class,
            PublisherSeeder::class,
            AuthorSeeder::class,
            AttributeSeeder::class,
            AttributeOptionSeeder::class,
            CategoryAttributeSeeder::class,

            // Phase 2 — Product Data
            ProductSeeder::class,
            ProductAuthorSeeder::class,
            ProductVariantSeeder::class,
            ProductImageSeeder::class,
            VariantImageSeeder::class,

            // Phase 3 — Dynamic Data
            ProductAttributeValueSeeder::class,
            VariantAttributeValueSeeder::class,
        ]);
    }
}
