<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Product;
use App\Models\ProductAuthor;
use Illuminate\Database\Seeder;

class ProductAuthorSeeder extends Seeder
{
    public function run(): void
    {
        $humayun = Author::where('slug', 'humayun-ahmed')->first();
        $jkRowling = Author::where('slug', 'jk-rowling')->first();

        // ── Himu → Humayun Ahmed ───────────────────────────────────────────

        $himu = Product::where('slug', 'himu')->first();
        if ($himu && $humayun) {
            ProductAuthor::firstOrCreate(
                ['product_id' => $himu->id, 'author_id' => $humayun->id],
                ['sort_order' => 1]
            );
        }

        // ── Misir Ali → Humayun Ahmed ──────────────────────────────────────

        $misirAli = Product::where('slug', 'misir-ali-samagra')->first();
        if ($misirAli && $humayun) {
            ProductAuthor::firstOrCreate(
                ['product_id' => $misirAli->id, 'author_id' => $humayun->id],
                ['sort_order' => 1]
            );
        }

        // ── Harry Potter → J.K. Rowling ────────────────────────────────────

        $harryPotter = Product::where('slug', 'harry-potter-and-the-philosophers-stone')->first();
        if ($harryPotter && $jkRowling) {
            ProductAuthor::firstOrCreate(
                ['product_id' => $harryPotter->id, 'author_id' => $jkRowling->id],
                ['sort_order' => 1]
            );
        }
    }
}
