<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $publishers = Publisher::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Publishers/Index', [
            'publishers' => $publishers,
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
        return Inertia::render('Admin/Publishers/Create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(StorePublisherRequest $request)
    {
        $logo = null;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store('publishers', 'public');
        }

        Publisher::create([
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug')
                ?: Str::slug($request->validated('name')),
            'logo' => $logo,
            'website' => $request->validated('website'),
            'description' => $request->validated('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Publisher created successfully.',
        ]);

        return redirect()->route('admin.publishers.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publisher $publisher)
    {
        return Inertia::render('Admin/Publishers/Edit', [
            'publisher' => $publisher,
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(
        UpdatePublisherRequest $request,
        Publisher $publisher
    ) {
        $logo = $publisher->logo;

        if ($request->hasFile('logo')) {

            if (
                $publisher->logo &&
                Storage::disk('public')->exists($publisher->logo)
            ) {
                Storage::disk('public')->delete($publisher->logo);
            }

            $logo = $request->file('logo')
                ->store('publishers', 'public');
        }

        $publisher->update([
            'name' => $request->validated('name'),
            'slug' => $request->validated('slug')
                ?: Str::slug($request->validated('name')),
            'logo' => $logo,
            'website' => $request->validated('website'),
            'description' => $request->validated('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Publisher updated successfully.',
        ]);

        return redirect()->route('admin.publishers.index');
    }

    /**
     * Remove the specified resource.
     */
    public function destroy(Publisher $publisher)
    {
        // Prevent deleting publishers that are assigned to products
        if ($publisher->products()->exists()) {

            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'Cannot delete a publisher that is assigned to products.',
            ]);

            return redirect()->route('admin.publishers.index');
        }

        $publisher->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Publisher deleted successfully.',
        ]);

        return redirect()->route('admin.publishers.index');
    }
}
