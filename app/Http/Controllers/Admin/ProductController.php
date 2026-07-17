<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Author;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategoryAttribute;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductAuthor;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Publisher;
use App\Models\VariantAttributeValue;
use App\Models\VariantImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
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
    public function create(): Response
    {
        $categories = Category::select('id', 'name', 'parent_id')
            ->orderBy('name')
            ->get();

        $brands = Brand::select('id', 'name')
            ->orderBy('name')
            ->get();

        $publishers = Publisher::select('id', 'name')
            ->orderBy('name')
            ->get();

        $authors = Author::select('id', 'name')
            ->orderBy('name')
            ->get();

        $selectedCategory = $categories->first();
        $attributes = collect();

        if ($selectedCategory) {
            $attributes = CategoryAttribute::with([
                'attribute.options',
            ])
                ->where('category_id', $selectedCategory->id)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($item) {
                    return $item->attribute;
                });
        }

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
            'brands' => $brands,
            'publishers' => $publishers,
            'authors' => $authors,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create([
                'category_id' => $request->validated('category_id'),
                'brand_id' => $request->validated('brand_id'),
                'publisher_id' => $request->validated('publisher_id'),
                'name' => $request->validated('name'),
                'slug' => $request->validated('slug'),
                'description' => $request->validated('description'),
                'weight' => $request->validated('weight'),
                'barcode' => $request->validated('barcode'),
                'featured' => $request->boolean('featured'),
                'status' => $request->validated('status'),
                'published_at' => $request->validated('published_at'),
                'meta_title' => $request->validated('meta_title'),
                'meta_description' => $request->validated('meta_description'),
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $path,
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                        'alt_text' => $product->name,
                    ]);
                }
            }

            if ($request->filled('authors')) {
                $authorData = collect($request->input('authors'))->map(
                    fn (array $author, int $index) => [
                        'product_id' => $product->id,
                        'author_id' => $author['author_id'],
                        'sort_order' => $author['sort_order'] ?? $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                )->toArray();

                ProductAuthor::insert($authorData);
            }

            if ($request->filled('attributes')) {
                $attributeData = collect($request->input('attributes'))->map(
                    fn (array $attr) => [
                        'product_id' => $product->id,
                        'attribute_id' => $attr['attribute_id'],
                        'attribute_option_id' => $attr['attribute_option_id'] ?? null,
                        'value_text' => $attr['value_text'] ?? null,
                        'value_number' => $attr['value_number'] ?? null,
                        'value_boolean' => $attr['value_boolean'] ?? null,
                        'value_date' => $attr['value_date'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                )->toArray();

                ProductAttributeValue::insert($attributeData);
            }

            if ($request->filled('variants')) {
                foreach ($request->input('variants') as $variantIndex => $variantData) {
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'],
                        'price' => $variantData['price'],
                        'discount_price' => $variantData['discount_price'] ?? null,
                        'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                        'is_default' => $variantData['is_default'] ?? ($variantIndex === 0),
                    ]);

                    if (! empty($variantData['attribute_option_ids'])) {
                        $variantAttrData = collect($variantData['attribute_option_ids'])->map(
                            fn (int $optionId) => [
                                'product_variant_id' => $variant->id,
                                'attribute_option_id' => $optionId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        )->toArray();

                        VariantAttributeValue::insert($variantAttrData);
                    }

                    $variantImageKey = "variants.{$variantIndex}.images";
                    if ($request->hasFile($variantImageKey)) {
                        foreach ($request->file($variantImageKey) as $imgIndex => $image) {
                            $path = $image->store('variants', 'public');

                            VariantImage::create([
                                'product_variant_id' => $variant->id,
                                'url' => $path,
                                'is_primary' => $imgIndex === 0,
                                'sort_order' => $imgIndex,
                            ]);
                        }
                    }
                }
            }

            return $product;
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Product created successfully.',
        ]);

        return redirect()->route('admin.products.index');
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
    public function edit(Product $product): Response
    {
        $product->load([
            'images',
            'authors.author',
            'attributeValues.attribute',
            'attributeValues.option',
            'variants.attributeValues.option',
            'variants.images',
        ]);

        $categories = Category::select('id', 'name', 'parent_id')
            ->orderBy('name')
            ->get();

        $brands = Brand::select('id', 'name')
            ->orderBy('name')
            ->get();

        $publishers = Publisher::select('id', 'name')
            ->orderBy('name')
            ->get();

        $authors = Author::select('id', 'name')
            ->orderBy('name')
            ->get();

        $attributes = CategoryAttribute::with(['attribute.options'])
            ->where('category_id', $product->category_id)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($item) => $item->attribute);

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'brands' => $brands,
            'publishers' => $publishers,
            'authors' => $authors,
            'attributes' => $attributes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update([
                'category_id' => $request->validated('category_id'),
                'brand_id' => $request->validated('brand_id'),
                'publisher_id' => $request->validated('publisher_id'),
                'name' => $request->validated('name'),
                'slug' => $request->validated('slug'),
                'description' => $request->validated('description'),
                'weight' => $request->validated('weight'),
                'barcode' => $request->validated('barcode'),
                'featured' => $request->boolean('featured'),
                'status' => $request->validated('status'),
                'published_at' => $request->validated('published_at'),
                'meta_title' => $request->validated('meta_title'),
                'meta_description' => $request->validated('meta_description'),
            ]);

            if ($request->filled('deleted_image_ids')) {
                $imagesToDelete = ProductImage::whereIn('id', $request->input('deleted_image_ids'))
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    if (Storage::disk('public')->exists($image->url)) {
                        Storage::disk('public')->delete($image->url);
                    }
                    $image->delete();
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('products', 'public');

                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => $path,
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                        'alt_text' => $product->name,
                    ]);
                }
            }

            $product->authors()->delete();

            if ($request->filled('authors')) {
                $authorData = collect($request->input('authors'))->map(
                    fn (array $author, int $index) => [
                        'product_id' => $product->id,
                        'author_id' => $author['author_id'],
                        'sort_order' => $author['sort_order'] ?? $index,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                )->toArray();

                ProductAuthor::insert($authorData);
            }

            $product->attributeValues()->delete();

            if ($request->filled('attributes')) {
                $attributeData = collect($request->input('attributes'))->map(
                    fn (array $attr) => [
                        'product_id' => $product->id,
                        'attribute_id' => $attr['attribute_id'],
                        'attribute_option_id' => $attr['attribute_option_id'] ?? null,
                        'value_text' => $attr['value_text'] ?? null,
                        'value_number' => $attr['value_number'] ?? null,
                        'value_boolean' => $attr['value_boolean'] ?? null,
                        'value_date' => $attr['value_date'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                )->toArray();

                ProductAttributeValue::insert($attributeData);
            }

            if ($request->filled('deleted_variant_ids')) {
                $variantsToDelete = ProductVariant::whereIn('id', $request->input('deleted_variant_ids'))
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($variantsToDelete as $variant) {
                    foreach ($variant->images as $variantImage) {
                        if (Storage::disk('public')->exists($variantImage->url)) {
                            Storage::disk('public')->delete($variantImage->url);
                        }
                    }
                    $variant->images()->delete();
                    $variant->attributeValues()->delete();
                    $variant->delete();
                }
            }

            if ($request->filled('variants')) {
                foreach ($request->input('variants') as $variantIndex => $variantData) {
                    if (! empty($variantData['id'])) {
                        $variant = ProductVariant::find($variantData['id']);
                        if ($variant && $variant->product_id === $product->id) {
                            $variant->update([
                                'sku' => $variantData['sku'],
                                'price' => $variantData['price'],
                                'discount_price' => $variantData['discount_price'] ?? null,
                                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                                'is_default' => $variantData['is_default'] ?? false,
                            ]);
                        } else {
                            continue;
                        }
                    } else {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'sku' => $variantData['sku'],
                            'price' => $variantData['price'],
                            'discount_price' => $variantData['discount_price'] ?? null,
                            'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                            'is_default' => $variantData['is_default'] ?? false,
                        ]);
                    }

                    $variant->attributeValues()->delete();

                    if (! empty($variantData['attribute_option_ids'])) {
                        $variantAttrData = collect($variantData['attribute_option_ids'])->map(
                            fn (int $optionId) => [
                                'product_variant_id' => $variant->id,
                                'attribute_option_id' => $optionId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        )->toArray();

                        VariantAttributeValue::insert($variantAttrData);
                    }
                }
            }
        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Product updated successfully.',
        ]);

        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            if (Storage::disk('public')->exists($image->url)) {
                Storage::disk('public')->delete($image->url);
            }
        }

        foreach ($product->variants as $variant) {
            foreach ($variant->images as $variantImage) {
                if (Storage::disk('public')->exists($variantImage->url)) {
                    Storage::disk('public')->delete($variantImage->url);
                }
            }
        }

        $product->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Product deleted successfully.',
        ]);

        return redirect()->route('admin.products.index');
    }
}
