<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeOptionRequest;
use App\Http\Requests\UpdateAttributeOptionRequest;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttributeOptionController extends Controller
{
    public function index(Request $request)
    {
        $attributeId = $request->integer('attribute_id');

        $options = AttributeOption::query()
            ->where('attribute_id', $attributeId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json($options);
    }

    public function create(Request $request)
    {
        $attributes = Attribute::query()
            ->orderBy('name')
            ->get(['id', 'name', 'input_type']);

        return Inertia::render('Admin/AttributeOptions/Create', [
            'attributes' => $attributes,
            'attributeId' => $request->integer('attribute_id') ?: null,
        ]);
    }

    public function store(StoreAttributeOptionRequest $request)
    {
        $option = AttributeOption::create($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Option created successfully.',
        ]);

        return redirect()->back();
    }

    public function show(AttributeOption $attributeOption)
    {
        //
    }

    public function edit(AttributeOption $attributeOption)
    {
        //
    }

    public function update(UpdateAttributeOptionRequest $request, AttributeOption $attributeOption)
    {
        $attributeOption->update($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Option updated successfully.',
        ]);

        return redirect()->back();
    }

    public function destroy(AttributeOption $attributeOption)
    {
        if ($attributeOption->productValues()->exists() || $attributeOption->variantValues()->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Cannot delete an option that is in use by products or variants.',
            ]);

            return redirect()->back();
        }

        $attributeOption->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Option deleted successfully.',
        ]);

        return redirect()->back();
    }
}
