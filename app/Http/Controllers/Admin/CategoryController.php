<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $categories = Category::query()
            ->with('parent')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): Response
    {
        $parents = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Categories/Create', [
            'parents' => $parents,
        ]);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request)
    {
        DB::transaction(function () use ($request) {
            $image = null;

            if ($request->hasFile('image')) {
                $image = $request->file('image')->store('categories', 'public');
            }

            Category::create([
                'parent_id' => $request->parent_id,
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'image' => $image,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->boolean('is_active'),
            ]);

        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Category created successfully.']);

        return redirect()->route('admin.categories.index');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): Response
    {
        $parents = Category::query()
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
            'parents' => $parents,
        ]);
    }

    /**
     * Update the specified category.
     */
    /**
     * Update the specified category.
     */
    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ) {
        DB::transaction(function () use ($request, $category) {

            $image = $category->image;

            if ($request->hasFile('image')) {
                if ($image) {
                    Storage::disk('public')->delete($image);
                }

                $image = $request->file('image')->store('categories', 'public');
            }

            $category->update([
                'parent_id' => $request->parent_id,
                'name' => $request->name,
                'slug' => $request->slug ?: Str::slug($request->name),
                'image' => $image,
                'sort_order' => $request->sort_order,
                'is_active' => $request->boolean('is_active'),
            ]);

        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Category updated successfully.']);

        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Optional: Prevent deleting categories that still have products
        if ($category->products()->exists()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => 'Cannot delete a category that contains products.']);

            return redirect()->route('admin.categories.index');
        }

        $category->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Category deleted successfully.']);

        return redirect()->route('admin.categories.index');
    }
}
