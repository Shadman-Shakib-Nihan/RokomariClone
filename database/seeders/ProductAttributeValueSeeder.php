<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Seeder;

class ProductAttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        // ── Books ─────────────────────────────────────────────────────────

        $this->setBookAttributes('himu', 320, 'bangla', '978-984-1234-56-7', '1st Edition', 'paperback');
        $this->setBookAttributes('misir-ali-samagra', 280, 'bangla', '978-984-1234-57-4', '2nd Edition', 'paperback');
        $this->setBookAttributes(
            slug: 'harry-potter-and-the-philosophers-stone',
            pages: 223,
            language: 'english',
            isbn: '978-0-7475-3269-9',
            edition: '1st Edition',
            coverType: 'paperback'
        );

        // ── iPhone 16 ──────────────────────────────────────────────────────

        $this->setPhoneAttributes('iphone-16', 8, 5000, 'A18 Pro', 180, 12);

        // ── Samsung Galaxy S25 ─────────────────────────────────────────────

        $this->setPhoneAttributes('samsung-galaxy-s25', 8, 4500, 'Exynos 2500', 185, 12);

        // ── MacBook Pro 14" ────────────────────────────────────────────────

        $this->setLaptopAttributes('macbook-pro-14', '512GB', 'M4 Pro', 1600);

        // ── Dell XPS 15 ────────────────────────────────────────────────────

        $this->setLaptopAttributes('dell-xps-15', '512GB', 'Intel Core Ultra 7', 1850);
    }

    private function setBookAttributes(
        string $slug,
        int $pages,
        string $language,
        string $isbn,
        string $edition,
        string $coverType,
    ): void {
        $product = Product::where('slug', $slug)->first();
        if (! $product) {
            return;
        }

        $this->setValue($product, 'pages', valueNumber: $pages);
        $this->setValue($product, 'language', optionSlug: $language);
        $this->setValue($product, 'isbn', valueText: $isbn);
        $this->setValue($product, 'edition', valueText: $edition);
        $this->setValue($product, 'cover-type', optionSlug: $coverType);
    }

    private function setPhoneAttributes(
        string $slug,
        int $ramGb,
        int $batteryMah,
        string $processor,
        int $weightG,
        int $warrantyMonths,
    ): void {
        $product = Product::where('slug', $slug)->first();
        if (! $product) {
            return;
        }

        $this->setValue($product, 'ram', optionSlug: "{$ramGb}gb");
        $this->setValue($product, 'battery', valueText: "{$batteryMah}mAh");
        $this->setValue($product, 'processor', valueText: $processor);
        $this->setValue($product, 'weight', valueNumber: $weightG);
        $this->setValue($product, 'warranty', valueText: "{$warrantyMonths} months");
    }

    private function setLaptopAttributes(
        string $slug,
        string $storage,
        string $processor,
        int $weightG,
    ): void {
        $product = Product::where('slug', $slug)->first();
        if (! $product) {
            return;
        }

        $this->setValue($product, 'storage', optionSlug: strtolower($storage));
        $this->setValue($product, 'processor', valueText: $processor);
        $this->setValue($product, 'weight', valueNumber: $weightG);
    }

    private function setValue(
        Product $product,
        string $attributeSlug,
        ?string $optionSlug = null,
        ?string $valueText = null,
        ?int $valueNumber = null,
    ): void {
        $attribute = Attribute::where('slug', $attributeSlug)->first();
        if (! $attribute) {
            return;
        }

        $data = [
            'product_id' => $product->id,
            'attribute_id' => $attribute->id,
        ];

        if ($optionSlug) {
            $option = AttributeOption::where('attribute_id', $attribute->id)
                ->where('value', $optionSlug)
                ->first();

            // Try matching case-insensitively
            if (! $option) {
                $option = AttributeOption::where('attribute_id', $attribute->id)
                    ->whereRaw('LOWER(value) = ?', [strtolower($optionSlug)])
                    ->first();
            }

            if ($option) {
                $data['attribute_option_id'] = $option->id;
            }
        }

        if ($valueText !== null) {
            $data['value_text'] = $valueText;
        }

        if ($valueNumber !== null) {
            $data['value_number'] = $valueNumber;
        }

        ProductAttributeValue::firstOrCreate(
            ['product_id' => $product->id, 'attribute_id' => $attribute->id],
            $data
        );
    }
}
