<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;


class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = $request->string('string')->tostring();

        $brands = Brand::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            - latest()
                ->paginate(10)
                ->withQueryString();

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Brands/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        DB::transaction(function () use ($request) {
            $logo = null;
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo')
                    ->store('brands', 'public');
            }

            Brand::create([
                'name' => $request->validated('name'),
                'slug' => $request->validated('slug'),
                'logo' => $logo,
            ]);

        });
        Inertia::flash('toast', ['type' => 'success', 'message' => 'Category created successfully.']);

        return redirect()->route('admin.brands.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return Inertia::render('Admin/Brands/Edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
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
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Brand updated successfully.']);

        return redirect()->route('admin.brands.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        // Prevent deleting a brand that is being used by products
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
