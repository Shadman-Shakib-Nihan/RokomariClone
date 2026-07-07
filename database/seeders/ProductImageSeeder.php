<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $himu = Product::where('slug', 'himu')->first();
        if ($himu) {
            $this->addImages($himu, [
                ['url' => '/images/himu/front.jpg', 'alt' => 'হিমু — প্রচ্ছদ', 'primary' => true],
                ['url' => '/images/himu/back.jpg', 'alt' => 'হিমু — পিছনের প্রচ্ছদ', 'primary' => false],
            ]);
        }

        $misirAli = Product::where('slug', 'misir-ali-samagra')->first();
        if ($misirAli) {
            $this->addImages($misirAli, [
                ['url' => '/images/misir-ali/front.jpg', 'alt' => 'মিসির আলি সমগ্র — প্রচ্ছদ', 'primary' => true],
                ['url' => '/images/misir-ali/back.jpg', 'alt' => 'মিসির আলি সমগ্র — পিছনের প্রচ্ছদ', 'primary' => false],
            ]);
        }

        $harryPotter = Product::where('slug', 'harry-potter-and-the-philosophers-stone')->first();
        if ($harryPotter) {
            $this->addImages($harryPotter, [
                ['url' => '/images/harry-potter-ps/front.jpg', 'alt' => 'Harry Potter — Front Cover', 'primary' => true],
                ['url' => '/images/harry-potter-ps/back.jpg', 'alt' => 'Harry Potter — Back Cover', 'primary' => false],
                ['url' => '/images/harry-potter-ps/spine.jpg', 'alt' => 'Harry Potter — Spine', 'primary' => false],
            ]);
        }

        $iphone = Product::where('slug', 'iphone-16')->first();
        if ($iphone) {
            $this->addImages($iphone, [
                ['url' => '/images/iphone-16/front.jpg', 'alt' => 'iPhone 16 — Front View', 'primary' => true],
                ['url' => '/images/iphone-16/back.jpg', 'alt' => 'iPhone 16 — Back View', 'primary' => false],
                ['url' => '/images/iphone-16/side.jpg', 'alt' => 'iPhone 16 — Side View', 'primary' => false],
                ['url' => '/images/iphone-16/packaging.jpg', 'alt' => 'iPhone 16 — Packaging', 'primary' => false],
            ]);
        }

        $s25 = Product::where('slug', 'samsung-galaxy-s25')->first();
        if ($s25) {
            $this->addImages($s25, [
                ['url' => '/images/s25/front.jpg', 'alt' => 'Galaxy S25 — Front View', 'primary' => true],
                ['url' => '/images/s25/back.jpg', 'alt' => 'Galaxy S25 — Back View', 'primary' => false],
                ['url' => '/images/s25/side.jpg', 'alt' => 'Galaxy S25 — Side View', 'primary' => false],
            ]);
        }

        $mbp = Product::where('slug', 'macbook-pro-14')->first();
        if ($mbp) {
            $this->addImages($mbp, [
                ['url' => '/images/mbp14/front.jpg', 'alt' => 'MacBook Pro 14" — Front View', 'primary' => true],
                ['url' => '/images/mbp14/keyboard.jpg', 'alt' => 'MacBook Pro 14" — Keyboard', 'primary' => false],
                ['url' => '/images/mbp14/side.jpg', 'alt' => 'MacBook Pro 14" — Side View', 'primary' => false],
            ]);
        }

        $xps = Product::where('slug', 'dell-xps-15')->first();
        if ($xps) {
            $this->addImages($xps, [
                ['url' => '/images/xps15/front.jpg', 'alt' => 'Dell XPS 15 — Front View', 'primary' => true],
                ['url' => '/images/xps15/keyboard.jpg', 'alt' => 'Dell XPS 15 — Keyboard', 'primary' => false],
                ['url' => '/images/xps15/side.jpg', 'alt' => 'Dell XPS 15 — Side View', 'primary' => false],
            ]);
        }
    }

    private function addImages(Product $product, array $images): void
    {
        foreach ($images as $index => $image) {
            ProductImage::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'url' => $image['url'],
                ],
                [
                    'sort_order' => $index + 1,
                    'is_primary' => $image['primary'],
                    'alt_text' => $image['alt'],
                ]
            );
        }
    }
}
