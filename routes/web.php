<?php

use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\AttributeOptionController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PublisherController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});
Route::inertia('show', 'Show')->name('show');

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('categories', CategoryController::class);

        Route::resource('brands', BrandController::class);

        Route::resource('publishers', PublisherController::class);

        Route::resource('authors', AuthorController::class);

        Route::resource('attributes', AttributeController::class);

        Route::resource('attribute-options', AttributeOptionController::class);

        Route::resource('products', ProductController::class);

    });

require __DIR__.'/settings.php';
