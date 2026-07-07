<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use Illuminate\Database\Seeder;

class VariantAttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        $colorAttr = Attribute::where('slug', 'color')->first();
        $storageAttr = Attribute::where('slug', 'storage')->first();
        $ramAttr = Attribute::where('slug', 'ram')->first();

        if (! $colorAttr || ! $storageAttr || ! $ramAttr) {
            return;
        }

        $colorOptions = AttributeOption::where('attribute_id', $colorAttr->id)
            ->pluck('id', 'value');
        $storageOptions = AttributeOption::where('attribute_id', $storageAttr->id)
            ->pluck('id', 'value');
        $ramOptions = AttributeOption::where('attribute_id', $ramAttr->id)
            ->pluck('id', 'value');

        // ── iPhone 16 variants ─────────────────────────────────────────────

        $this->addStorageColorVariant('IP16-128', '128GB', 'Black', $storageOptions, $colorOptions);
        $this->addStorageColorVariant('IP16-256', '256GB', 'White', $storageOptions, $colorOptions);
        $this->addStorageColorVariant('IP16-512', '512GB', 'Blue', $storageOptions, $colorOptions);

        // ── Galaxy S25 variants ────────────────────────────────────────────

        $this->addStorageColorVariant('SGS25-128', '128GB', 'Black', $storageOptions, $colorOptions);
        $this->addStorageColorVariant('SGS25-256', '256GB', 'White', $storageOptions, $colorOptions);

        // ── MacBook Pro 14" variants ───────────────────────────────────────

        $this->addRamVariant('MBP14-16', '16GB', $ramOptions);
        // MBP14-32 (32GB) has no matching RAM option — linked via SKU/price only

        // ── Dell XPS 15 variants ───────────────────────────────────────────

        $this->addRamVariant('XPS15-8', '8GB', $ramOptions);
        $this->addRamVariant('XPS15-16', '16GB', $ramOptions);
    }

    private function addStorageColorVariant(
        string $sku,
        string $storageValue,
        string $colorValue,
        $storageOptions,
        $colorOptions,
    ): void {
        $variant = ProductVariant::where('sku', $sku)->first();
        if (! $variant) {
            return;
        }

        $storageOptionId = $storageOptions[$storageValue] ?? null;
        $colorOptionId = $colorOptions[$colorValue] ?? null;

        if ($storageOptionId) {
            VariantAttributeValue::firstOrCreate(
                [
                    'product_variant_id' => $variant->id,
                    'attribute_option_id' => $storageOptionId,
                ]
            );
        }

        if ($colorOptionId) {
            VariantAttributeValue::firstOrCreate(
                [
                    'product_variant_id' => $variant->id,
                    'attribute_option_id' => $colorOptionId,
                ]
            );
        }
    }

    private function addRamVariant(string $sku, string $ramValue, $ramOptions): void
    {
        $variant = ProductVariant::where('sku', $sku)->first();
        if (! $variant) {
            return;
        }

        $ramOptionId = $ramOptions[$ramValue] ?? null;

        if ($ramOptionId) {
            VariantAttributeValue::firstOrCreate(
                [
                    'product_variant_id' => $variant->id,
                    'attribute_option_id' => $ramOptionId,
                ]
            );
        }
    }
}
