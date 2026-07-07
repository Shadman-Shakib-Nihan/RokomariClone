<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Database\Seeder;

class AttributeOptionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Color options ──────────────────────────────────────────────────

        $color = Attribute::where('slug', 'color')->first();
        if ($color) {
            $this->createOptions($color, ['Black', 'White', 'Blue', 'Red']);
        }

        // ── Storage options ────────────────────────────────────────────────

        $storage = Attribute::where('slug', 'storage')->first();
        if ($storage) {
            $this->createOptions($storage, ['64GB', '128GB', '256GB', '512GB']);
        }

        // ── RAM options ────────────────────────────────────────────────────

        $ram = Attribute::where('slug', 'ram')->first();
        if ($ram) {
            $this->createOptions($ram, ['4GB', '8GB', '16GB']);
        }

        // ── Cover Type options ─────────────────────────────────────────────

        $coverType = Attribute::where('slug', 'cover-type')->first();
        if ($coverType) {
            $this->createOptions($coverType, ['Hardcover', 'Paperback']);
        }

        // ── Language options ───────────────────────────────────────────────

        $language = Attribute::where('slug', 'language')->first();
        if ($language) {
            $this->createOptions($language, ['English', 'Bangla']);
        }
    }

    /**
     * Create attribute options with auto-incrementing sort order.
     */
    private function createOptions(Attribute $attribute, array $values): void
    {
        foreach ($values as $index => $value) {
            AttributeOption::firstOrCreate(
                [
                    'attribute_id' => $attribute->id,
                    'value' => $value,
                ],
                [
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
