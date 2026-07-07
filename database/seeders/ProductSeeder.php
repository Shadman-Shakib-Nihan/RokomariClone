<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Publisher;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $fiction = Category::where('slug', 'fiction-books')->first();
        $mobile = Category::where('slug', 'mobile-phones')->first();
        $laptop = Category::where('slug', 'laptops')->first();

        $anannya = Publisher::where('slug', 'anannya')->first();
        $penguin = Publisher::where('slug', 'penguin-random-house')->first();

        $apple = Brand::where('slug', 'apple')->first();
        $samsung = Brand::where('slug', 'samsung')->first();
        $dell = Brand::where('slug', 'dell')->first();

        // ── Books ─────────────────────────────────────────────────────────

        Product::firstOrCreate(
            ['slug' => 'himu'],
            [
                'category_id' => $fiction?->id,
                'publisher_id' => $anannya?->id,
                'name' => 'হিমু',
                'slug' => 'himu',
                'description' => 'হিমু সিরিজের প্রথম বই। হিমু একজন অলস, উদাসীন কিন্তু মেধাবী তরুণ যে তার নিজস্ব জগতে বাস করে।',
                'status' => 'active',
                'published_at' => now(),
                'featured' => true,
            ]
        );

        Product::firstOrCreate(
            ['slug' => 'misir-ali-samagra'],
            [
                'category_id' => $fiction?->id,
                'publisher_id' => $anannya?->id,
                'name' => 'মিসির আলি সমগ্র',
                'slug' => 'misir-ali-samagra',
                'description' => 'মিসির আলি সিরিজের সব গল্প একত্রে। মিসির আলি একজন রহস্যপ্রিয় মনস্তাত্ত্বিক বিশ্লেষক।',
                'status' => 'active',
                'published_at' => now(),
                'featured' => true,
            ]
        );

        Product::firstOrCreate(
            ['slug' => 'harry-potter-and-the-philosophers-stone'],
            [
                'category_id' => $fiction?->id,
                'publisher_id' => $penguin?->id,
                'name' => 'Harry Potter and the Philosopher\'s Stone',
                'slug' => 'harry-potter-and-the-philosophers-stone',
                'description' => 'The first novel in the Harry Potter series. Harry discovers he is a wizard on his eleventh birthday.',
                'status' => 'active',
                'published_at' => now(),
                'featured' => true,
            ]
        );

        // ── Mobile Phones ─────────────────────────────────────────────────

        Product::firstOrCreate(
            ['slug' => 'iphone-16'],
            [
                'category_id' => $mobile?->id,
                'brand_id' => $apple?->id,
                'name' => 'iPhone 16',
                'slug' => 'iphone-16',
                'description' => 'Apple iPhone 16 with A18 chip, advanced camera system, and stunning display.',
                'status' => 'active',
                'published_at' => now(),
                'featured' => true,
            ]
        );

        Product::firstOrCreate(
            ['slug' => 'samsung-galaxy-s25'],
            [
                'category_id' => $mobile?->id,
                'brand_id' => $samsung?->id,
                'name' => 'Samsung Galaxy S25',
                'slug' => 'samsung-galaxy-s25',
                'description' => 'Samsung Galaxy S25 with advanced AI features, powerful camera, and sleek design.',
                'status' => 'active',
                'published_at' => now(),
                'featured' => true,
            ]
        );

        // ── Laptops ───────────────────────────────────────────────────────

        Product::firstOrCreate(
            ['slug' => 'macbook-pro-14'],
            [
                'category_id' => $laptop?->id,
                'brand_id' => $apple?->id,
                'name' => 'MacBook Pro 14"',
                'slug' => 'macbook-pro-14',
                'description' => 'Apple MacBook Pro 14-inch with M4 chip, Liquid Retina XDR display, and all-day battery life.',
                'status' => 'active',
                'published_at' => now(),
                'featured' => true,
            ]
        );

        Product::firstOrCreate(
            ['slug' => 'dell-xps-15'],
            [
                'category_id' => $laptop?->id,
                'brand_id' => $dell?->id,
                'name' => 'Dell XPS 15',
                'slug' => 'dell-xps-15',
                'description' => 'Dell XPS 15 with Intel Core Ultra processor, OLED display, and premium build quality.',
                'status' => 'active',
                'published_at' => now(),
                'featured' => true,
            ]
        );
    }
}
