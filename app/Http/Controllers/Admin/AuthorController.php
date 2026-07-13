<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $authors = Author::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Authors/Index', [
            'authors' => $authors,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Authors/Create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(StoreAuthorRequest $request)
    {
        $photo = null;

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('authors', 'public');
        }

        DB::transaction(function () use ($request, $photo) {

            Author::create([
                'name' => $request->validated('name'),
                'slug' => $request->validated('slug')
                    ?: Str::slug($request->validated('name')),
                'photo' => $photo,
                'biography' => $request->validated('biography'),
                'is_active' => $request->boolean('is_active'),
            ]);

        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Author created successfully.',
        ]);

        return redirect()->route('admin.authors.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Author $author): Response
    {
        return Inertia::render('Admin/Authors/Edit', [
            'author' => $author,
        ]);
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateAuthorRequest $request, Author $author)
    {
        $photo = $author->photo;

        if ($request->hasFile('photo')) {

            if (
                $author->photo &&
                Storage::disk('public')->exists($author->photo)
            ) {
                Storage::disk('public')->delete($author->photo);
            }

            $photo = $request->file('photo')->store('authors', 'public');
        }

        DB::transaction(function () use ($request, $author, $photo) {

            $author->update([
                'name' => $request->validated('name'),
                'slug' => $request->validated('slug')
                    ?: Str::slug($request->validated('name')),
                'photo' => $photo,
                'biography' => $request->validated('biography'),
                'is_active' => $request->boolean('is_active'),
            ]);

        });

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Author updated successfully.',
        ]);

        return redirect()->route('admin.authors.index');
    }
}
