<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Database\Seeder;

class CategoryAttributeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Books → Book Attributes ────────────────────────────────────────

        $books = Category::where('slug', 'books')->first();
        if ($books) {
            $this->assignAttributes($books, [
                'pages' => ['is_required' => true, 'is_filterable' => false],
                'language' => ['is_required' => true, 'is_filterable' => true],
                'isbn' => ['is_required' => true, 'is_filterable' => false],
                'edition' => ['is_required' => false, 'is_filterable' => false],
                'cover-type' => ['is_required' => true, 'is_filterable' => true],
            ]);
        }

        // ── Mobile Phones → Electronics Attributes ─────────────────────────

        $mobilePhones = Category::where('slug', 'mobile-phones')->first();
        if ($mobilePhones) {
            $this->assignAttributes($mobilePhones, [
                'ram' => ['is_required' => true, 'is_filterable' => true],
                'storage' => ['is_required' => true, 'is_filterable' => true],
                'battery' => ['is_required' => true, 'is_filterable' => false],
                'color' => ['is_required' => true, 'is_filterable' => true],
            ]);
        }

        // ── Laptops → Electronics Attributes ───────────────────────────────

        $laptops = Category::where('slug', 'laptops')->first();
        if ($laptops) {
            $this->assignAttributes($laptops, [
                'ram' => ['is_required' => true, 'is_filterable' => true],
                'storage' => ['is_required' => true, 'is_filterable' => true],
                'processor' => ['is_required' => true, 'is_filterable' => true],
                'weight' => ['is_required' => false, 'is_filterable' => false],
            ]);
        }
    }

    /**
     * Assign attributes to a category.
     *
     * @param  array<string, array{is_required: bool, is_filterable: bool}>  $attributes
     */
    private function assignAttributes(Category $category, array $attributes): void
    {
        $sortOrder = 1;

        foreach ($attributes as $slug => $config) {
            $attribute = Attribute::where('slug', $slug)->first();

            if (! $attribute) {
                continue;
            }

            CategoryAttribute::firstOrCreate(
                [
                    'category_id' => $category->id,
                    'attribute_id' => $attribute->id,
                ],
                [
                    'is_required' => $config['is_required'],
                    'is_filterable' => $config['is_filterable'],
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }
}
