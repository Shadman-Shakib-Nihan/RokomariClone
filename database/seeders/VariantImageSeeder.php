<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\VariantImage;
use Illuminate\Database\Seeder;

class VariantImageSeeder extends Seeder
{
    public function run(): void
    {
        // ── iPhone 16 variants — each storage tier gets a separate image ────

        $this->addVariantImages('IP16-128', [
            ['url' => '/images/iphone-16/128/black-front.jpg', 'primary' => true],
            ['url' => '/images/iphone-16/128/black-back.jpg', 'primary' => false],
        ]);

        $this->addVariantImages('IP16-256', [
            ['url' => '/images/iphone-16/256/white-front.jpg', 'primary' => true],
            ['url' => '/images/iphone-16/256/white-back.jpg', 'primary' => false],
        ]);

        $this->addVariantImages('IP16-512', [
            ['url' => '/images/iphone-16/512/blue-front.jpg', 'primary' => true],
            ['url' => '/images/iphone-16/512/blue-back.jpg', 'primary' => false],
        ]);

        // ── Galaxy S25 variants ────────────────────────────────────────────

        $this->addVariantImages('SGS25-128', [
            ['url' => '/images/s25/128/navy-front.jpg', 'primary' => true],
            ['url' => '/images/s25/128/navy-back.jpg', 'primary' => false],
        ]);

        $this->addVariantImages('SGS25-256', [
            ['url' => '/images/s25/256/silver-front.jpg', 'primary' => true],
            ['url' => '/images/s25/256/silver-back.jpg', 'primary' => false],
        ]);

        // ── MacBook Pro 14" variants ───────────────────────────────────────

        $this->addVariantImages('MBP14-16', [
            ['url' => '/images/mbp14/16gb/space-gray.jpg', 'primary' => true],
        ]);

        $this->addVariantImages('MBP14-32', [
            ['url' => '/images/mbp14/32gb/silver.jpg', 'primary' => true],
        ]);

        // ── Dell XPS 15 variants ───────────────────────────────────────────

        $this->addVariantImages('XPS15-8', [
            ['url' => '/images/xps15/8gb/platinum.jpg', 'primary' => true],
        ]);

        $this->addVariantImages('XPS15-16', [
            ['url' => '/images/xps15/16gb/graphite.jpg', 'primary' => true],
        ]);
    }

    private function addVariantImages(string $sku, array $images): void
    {
        $variant = ProductVariant::where('sku', $sku)->first();
        if (! $variant) {
            return;
        }

        foreach ($images as $index => $image) {
            VariantImage::firstOrCreate(
                [
                    'product_variant_id' => $variant->id,
                    'url' => $image['url'],
                ],
                [
                    'sort_order' => $index + 1,
                    'is_primary' => $image['primary'],
                ]
            );
        }
    }
}
