<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // ── Parent Categories ──────────────────────────────────────────────

        $books = Category::firstOrCreate(
            ['slug' => 'books'],
            [
                'name' => 'Books',
                'slug' => 'books',
                'description' => 'All kinds of books including academic, fiction, Islamic, and children books.',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $electronics = Category::firstOrCreate(
            ['slug' => 'electronics'],
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'description' => 'Mobile phones, laptops, accessories, and more.',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $fashion = Category::firstOrCreate(
            ['slug' => 'fashion'],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'description' => 'Clothing, accessories, and lifestyle products.',
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        $stationery = Category::firstOrCreate(
            ['slug' => 'stationery'],
            [
                'name' => 'Stationery',
                'slug' => 'stationery',
                'description' => 'Office and school supplies, paper products, and writing instruments.',
                'is_active' => true,
                'sort_order' => 4,
            ]
        );

        // ── Books Children ─────────────────────────────────────────────────

        $this->childCategory($books, 'Academic Books', 'academic-books', 1);
        $this->childCategory($books, 'Fiction Books', 'fiction-books', 2);
        $this->childCategory($books, 'Islamic Books', 'islamic-books', 3);
        $this->childCategory($books, 'Children Books', 'children-books', 4);

        // ── Electronics Children ───────────────────────────────────────────

        $this->childCategory($electronics, 'Mobile Phones', 'mobile-phones', 1);
        $this->childCategory($electronics, 'Laptops', 'laptops', 2);
        $this->childCategory($electronics, 'Accessories', 'accessories', 3);
    }

    /**
     * Create a child category under the given parent.
     */
    private function childCategory(Category $parent, string $name, string $slug, int $sortOrder): void
    {
        Category::firstOrCreate(
            ['slug' => $slug],
            [
                'parent_id' => $parent->id,
                'name' => $name,
                'slug' => $slug,
                'is_active' => true,
                'sort_order' => $sortOrder,
            ]
        );
    }
}
