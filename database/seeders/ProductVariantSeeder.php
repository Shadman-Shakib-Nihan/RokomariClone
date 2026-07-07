<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // ── Himu — Default Variant ─────────────────────────────────────────

        $himu = Product::where('slug', 'himu')->first();
        if ($himu) {
            $this->createDefaultVariant($himu, 'HIMU-PB-001', 320.00, null, 200);
        }

        // ── Misir Ali — Default Variant ────────────────────────────────────

        $misirAli = Product::where('slug', 'misir-ali-samagra')->first();
        if ($misirAli) {
            $this->createDefaultVariant($misirAli, 'MISIR-PB-001', 380.00, null, 150);
        }

        // ── Harry Potter — Paperback + Hardcover ───────────────────────────

        $harryPotter = Product::where('slug', 'harry-potter-and-the-philosophers-stone')->first();
        if ($harryPotter) {
            $this->createDefaultVariant($harryPotter, 'HP-PS-PB-001', 450.00, 399.00, 300);

            ProductVariant::firstOrCreate(
                ['sku' => 'HP-PS-HC-001'],
                [
                    'product_id' => $harryPotter->id,
                    'price' => 750.00,
                    'discount_price' => 650.00,
                    'stock_quantity' => 100,
                    'is_default' => false,
                ]
            );
        }

        // ── iPhone 16 — 128GB / 256GB / 512GB ──────────────────────────────

        $iphone = Product::where('slug', 'iphone-16')->first();
        if ($iphone) {
            ProductVariant::firstOrCreate(
                ['sku' => 'IP16-128'],
                [
                    'product_id' => $iphone->id,
                    'price' => 99900.00,
                    'discount_price' => 94900.00,
                    'stock_quantity' => 50,
                    'is_default' => true,
                ]
            );

            ProductVariant::firstOrCreate(
                ['sku' => 'IP16-256'],
                [
                    'product_id' => $iphone->id,
                    'price' => 109900.00,
                    'discount_price' => 104900.00,
                    'stock_quantity' => 30,
                    'is_default' => false,
                ]
            );

            ProductVariant::firstOrCreate(
                ['sku' => 'IP16-512'],
                [
                    'product_id' => $iphone->id,
                    'price' => 129900.00,
                    'discount_price' => 124900.00,
                    'stock_quantity' => 20,
                    'is_default' => false,
                ]
            );
        }

        // ── Samsung Galaxy S25 — 128GB / 256GB ─────────────────────────────

        $s25 = Product::where('slug', 'samsung-galaxy-s25')->first();
        if ($s25) {
            ProductVariant::firstOrCreate(
                ['sku' => 'SGS25-128'],
                [
                    'product_id' => $s25->id,
                    'price' => 89900.00,
                    'discount_price' => 84900.00,
                    'stock_quantity' => 60,
                    'is_default' => true,
                ]
            );

            ProductVariant::firstOrCreate(
                ['sku' => 'SGS25-256'],
                [
                    'product_id' => $s25->id,
                    'price' => 99900.00,
                    'discount_price' => 94900.00,
                    'stock_quantity' => 40,
                    'is_default' => false,
                ]
            );
        }

        // ── MacBook Pro 14" — 16GB / 32GB ──────────────────────────────────

        $mbp = Product::where('slug', 'macbook-pro-14')->first();
        if ($mbp) {
            ProductVariant::firstOrCreate(
                ['sku' => 'MBP14-16'],
                [
                    'product_id' => $mbp->id,
                    'price' => 199900.00,
                    'discount_price' => 194900.00,
                    'stock_quantity' => 25,
                    'is_default' => true,
                ]
            );

            ProductVariant::firstOrCreate(
                ['sku' => 'MBP14-32'],
                [
                    'product_id' => $mbp->id,
                    'price' => 249900.00,
                    'discount_price' => 244900.00,
                    'stock_quantity' => 15,
                    'is_default' => false,
                ]
            );
        }

        // ── Dell XPS 15 — 8GB / 16GB ───────────────────────────────────────

        $xps = Product::where('slug', 'dell-xps-15')->first();
        if ($xps) {
            ProductVariant::firstOrCreate(
                ['sku' => 'XPS15-8'],
                [
                    'product_id' => $xps->id,
                    'price' => 149900.00,
                    'discount_price' => 144900.00,
                    'stock_quantity' => 20,
                    'is_default' => true,
                ]
            );

            ProductVariant::firstOrCreate(
                ['sku' => 'XPS15-16'],
                [
                    'product_id' => $xps->id,
                    'price' => 179900.00,
                    'discount_price' => 174900.00,
                    'stock_quantity' => 10,
                    'is_default' => false,
                ]
            );
        }
    }

    private function createDefaultVariant(
        Product $product,
        string $sku,
        float $price,
        ?float $discountPrice,
        int $stock,
    ): void {
        ProductVariant::firstOrCreate(
            ['sku' => $sku],
            [
                'product_id' => $product->id,
                'price' => $price,
                'discount_price' => $discountPrice,
                'stock_quantity' => $stock,
                'is_default' => true,
            ]
        );
    }
}
