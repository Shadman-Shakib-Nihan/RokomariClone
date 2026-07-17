<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $attributes = Attribute::query()
            ->withCount('options')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Attributes/Index', [
            'attributes' => $attributes,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Attributes/Create', [
            'inputTypes' => [
                ['value' => 'text', 'label' => 'Text'],
                ['value' => 'select', 'label' => 'Select (Dropdown)'],
                ['value' => 'boolean', 'label' => 'Yes/No'],
                ['value' => 'date', 'label' => 'Date'],
                ['value' => 'number', 'label' => 'Number'],
            ],
            'categories' => Category::select('id', 'name', 'parent_id')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreAttributeRequest $request)
    {
        $attribute = Attribute::create($request->safe()->except('categories'));

        if ($request->filled('categories')) {
            $attribute->categoryAttributes()->createMany(
                collect($request->input('categories'))->map(fn ($cat) => [
                    'category_id' => $cat['category_id'],
                    'sort_order' => $cat['sort_order'] ?? 0,
                    'is_required' => $cat['is_required'] ?? false,
                    'is_filterable' => $cat['is_filterable'] ?? false,
                ])->toArray(),
            );
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Attribute created successfully.',
        ]);

        return redirect()->route('admin.attributes.index');
    }

    public function edit(Attribute $attribute)
    {
        $attribute->load(['options', 'categoryAttributes']);

        return Inertia::render('Admin/Attributes/Edit', [
            'attribute' => $attribute,
            'inputTypes' => [
                ['value' => 'text', 'label' => 'Text'],
                ['value' => 'select', 'label' => 'Select (Dropdown)'],
                ['value' => 'boolean', 'label' => 'Yes/No'],
                ['value' => 'date', 'label' => 'Date'],
                ['value' => 'number', 'label' => 'Number'],
            ],
            'categories' => Category::select('id', 'name', 'parent_id')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $attribute->update($request->safe()->except('categories'));

        $attribute->categoryAttributes()->delete();

        if ($request->filled('categories')) {
            $attribute->categoryAttributes()->createMany(
                collect($request->input('categories'))->map(fn ($cat) => [
                    'category_id' => $cat['category_id'],
                    'sort_order' => $cat['sort_order'] ?? 0,
                    'is_required' => $cat['is_required'] ?? false,
                    'is_filterable' => $cat['is_filterable'] ?? false,
                ])->toArray(),
            );
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Attribute updated successfully.',
        ]);

        return redirect()->route('admin.attributes.index');
    }

    public function destroy(Attribute $attribute)
    {
        if ($attribute->productValues()->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Cannot delete an attribute that is in use by products.',
            ]);

            return redirect()->route('admin.attributes.index');
        }

        $attribute->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Attribute deleted successfully.',
        ]);

        return redirect()->route('admin.attributes.index');
    }
}
