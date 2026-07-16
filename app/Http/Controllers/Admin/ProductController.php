<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = request()->string('search')->toString();

        $filters = $request->only([
            'search',
            'status',
            'category_id',
            'brand_id',
            'publisher_id',

        ]);

        $product = Product::query()
            ->with(['category:id,name', 'brand:id,name', 'publisher:id,name'])

            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($filters['brand_id'] ?? null, function ($query, $brandId) {
                $query->where('brand_id', $brandId);
            })
            ->when($filters['publisher_id'] ?? null, function ($query, $publisherId) {
                $query->where('publisher_id', $publisherId);
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $product,
            'filters' => $filters,

            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'brands' => Brand::select('id', 'name')->orderBy('name')->get(),
            'publishers' => Publisher::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
