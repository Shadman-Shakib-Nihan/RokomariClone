<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('brand can be created with selected categories', function () {
    $user = User::factory()->create();
    Category::query()
        ->insert([
            ['name' => 'Fiction', 'slug' => 'fiction', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'slug' => 'science', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

    $categoryIds = Category::query()->pluck('id')->all();

    $this->actingAs($user)
        ->post(route('admin.brands.store'), [
            'name' => 'Acme Books',
            'slug' => 'acme-books',
            'is_active' => true,
            'categories' => $categoryIds,
        ])
        ->assertRedirect(route('admin.brands.index'));

    $brand = Brand::query()->where('slug', 'acme-books')->firstOrFail();

    expect($brand->categories()->pluck('categories.id')->all())
        ->toEqualCanonicalizing($categoryIds);
});

test('brand categories can be updated', function () {
    $user = User::factory()->create();
    Category::query()
        ->insert([
            ['name' => 'Fiction', 'slug' => 'fiction', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'slug' => 'science', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'History', 'slug' => 'history', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

    $initialCategory = Category::query()->where('slug', 'fiction')->firstOrFail();
    $updatedCategoryIds = Category::query()
        ->whereIn('slug', ['science', 'history'])
        ->pluck('id')
        ->all();
    $brand = Brand::query()->create([
        'name' => 'Acme Books',
        'slug' => 'acme-books',
        'is_active' => true,
    ]);
    $brand->categories()->sync([$initialCategory->id]);

    $this->actingAs($user)
        ->put(route('admin.brands.update', $brand), [
            'name' => 'Acme Books',
            'slug' => 'acme-books',
            'is_active' => true,
            'categories' => $updatedCategoryIds,
        ])
        ->assertRedirect(route('admin.brands.index'));

    expect($brand->refresh()->categories()->pluck('categories.id')->all())
        ->toEqualCanonicalizing($updatedCategoryIds);
});
