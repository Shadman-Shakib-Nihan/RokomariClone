<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAuthor extends Model
{
    protected $fillable = [
        'product_id',
        'author_id',
        'sort_order',
    ];

    /**
     * Book
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Author
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
