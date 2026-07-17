<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display the frontend shop/listing page.
     *
     * This method queries the database for all data needed by the
     * frontend Show.vue page, including categories, products (grouped
     * by featured, discounted, newly released, etc.), authors, and brands.
     *
     * Database calls:
     *   1. categories  — active top-level categories for the "Shop By Category" section
     *   2. products    — featured products for "শুধু আপনার জন্য" section
     *   3. products    — products with discount_price variants for "Quick Deal" / discounts
     *   4. products    — latest active products for "Newly Released" / "Recently Sold"
     *   5. authors     — all authors for "Buy Books of Top Authors"
     *   6. brands      — all brands for "Shop From Top Brands"
     *   7. products    — grouped by category for the academic/Islamic/language/novel book grids
     *
     * Each query uses Eloquent ORM which translates to efficient SQL.
     * Relationships (like variants, images) are eager-loaded with ->with()
     * to avoid the N+1 query problem.
     */
    public function show(Request $request): Response
    {
        /*
         * ── 1. Categories ──────────────────────────────────────────────
         * Fetch all active categories that have no parent (top-level),
         * ordered by sort_order. These populate the "Shop By Category"
         * grid. The `is_active` filter ensures deleted/inactive categories
         * are excluded.
         */
        $categories = Category::query()
            ->whereNull('parent_id')    // Only top-level categories
            ->where('is_active', true)  // Only active ones
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'image', 'icon']);

        /*
         * ── 2. Featured Products (শুধু আপনার জন্য) ─────────────────────
         * Products marked as `featured = true` and with status 'active'.
         * We eager-load the primary image and the default variant to
         * display price/discount info without extra queries per product.
         */
        $featuredProducts = Product::query()
            ->where('featured', true)
            ->where('status', 'active')
            ->with([
                'images' => fn ($q) => $q->where('is_primary', true)->limit(1),
                'variants' => fn ($q) => $q->where('is_default', true)->limit(1),
            ])
            ->limit(12)
            ->get();

        /*
         * ── 3. Discounted Products (Quick Deal) ────────────────────────
         * Products that have at least one variant with a discount_price.
         * We use whereHas() to create an EXISTS SQL subquery, which is
         * more efficient than loading all variants and filtering in PHP.
         */
        $discountedProducts = Product::query()
            ->where('status', 'active')
            ->whereHas('variants', fn ($q) => $q->whereNotNull('discount_price'))
            ->with([
                'images' => fn ($q) => $q->where('is_primary', true)->limit(1),
                'variants' => fn ($q) => $q->whereNotNull('discount_price')->limit(1),
            ])
            ->limit(12)
            ->get();

        /*
         * ── 4. Newly Released Products ─────────────────────────────────
         * Most recently created active products. Used for both "Newly
         * Released" and "Recently Sold" sections of the page.
         */
        $newProducts = Product::query()
            ->where('status', 'active')
            ->with([
                'images' => fn ($q) => $q->where('is_primary', true)->limit(1),
                'variants' => fn ($q) => $q->where('is_default', true)->limit(1),
            ])
            ->latest()                    // ORDER BY created_at DESC
            ->limit(12)
            ->get();

        /*
         * ── 5. Authors ─────────────────────────────────────────────────
         * All authors in the database for the "Buy Books of Top Authors"
         * section. Only active authors are included.
         */
        $authors = Author::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'photo']);

        /*
         * ── 6. Brands ──────────────────────────────────────────────────
         * All brands for the "Shop From Top Brands" carousel.
         */
        $brands = Brand::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'logo']);

        /*
         * ── 7. Book Grids (Category → Products) ───────────────────────
         * For each book-related subcategory under "Books", fetch up to 4
         * products. This powers the "একাডেমিক", "ইসলামি", "ভাষা ও অভিধান",
         * "উপন্যাস" grid sections.
         *
         * We do this in a single query using map+load to avoid N queries.
         */
        $bookGridCategoryIds = [5, 6, 7, 8]; // Academic, Fiction, Islamic, Children
        $bookGrids = Category::whereIn('id', $bookGridCategoryIds)
            ->with([
                'products' => fn ($q) => $q
                    ->where('status', 'active')
                    ->with([
                        'images' => fn ($iq) => $iq->where('is_primary', true)->limit(1),
                        'variants' => fn ($vq) => $vq->where('is_default', true)->limit(1),
                    ])
                    ->limit(4),
            ])
            ->get(['id', 'name', 'slug']);

        /*
         * ── 8. Render the page ─────────────────────────────────────────
         * Pass all data as props to the Inertia page component.
         * On the frontend, these props are available via
         * defineProps<{ ... }>().
         */
        return Inertia::render('Show', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'discountedProducts' => $discountedProducts,
            'newProducts' => $newProducts,
            'authors' => $authors,
            'brands' => $brands,
            'bookGrids' => $bookGrids,
        ]);
    }
}
