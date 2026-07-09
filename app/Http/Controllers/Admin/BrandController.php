<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $brands = Brand::query()
            ->with('categories:id,name')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        $categories = Category::select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Brands/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreBrandRequest $request)
    {
        DB::transaction(function () use ($request) {
            $logo = null;
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo')
                    ->store('brands', 'public');
            }

            $brand = Brand::create([
                'name' => $request->validated('name'),
                'slug' => $request->validated('slug'),
                'logo' => $logo,
                'description' => $request->validated('description'),
                'website' => $request->validated('website'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            $brand->categories()->sync($request->validated('categories', []));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Brand created successfully.']);

        return redirect()->route('admin.brands.index');
    }

    public function edit(Brand $brand)
    {
        $brand->load('categories:id,name');

        $categories = Category::select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Brands/Edit', [
            'brand' => $brand,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        DB::transaction(function () use ($request, $brand) {
            $logo = $brand->logo;

            if ($request->hasFile('logo')) {
                if ($logo) {
                    Storage::disk('public')->delete($logo);
                }
                $logo = $request->file('logo')
                    ->store('brands', 'public');
            }

            $brand->update([
                'name' => $request->validated('name'),
                'slug' => $request->validated('slug'),
                'logo' => $logo,
                'description' => $request->validated('description'),
                'website' => $request->validated('website'),
                'is_active' => $request->boolean('is_active', true),
            ]);

            $brand->categories()->sync($request->validated('categories', []));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Brand updated successfully.']);

        return redirect()->route('admin.brands.index');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Cannot delete a brand that is assigned to products.',
            ]);

            return redirect()->route('admin.brands.index');
        }

        $brand->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Brand deleted successfully.',
        ]);

        return redirect()->route('admin.brands.index');
    }
}
