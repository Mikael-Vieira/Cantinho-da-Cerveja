<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['category_id', 'name', 'slug', 'description', 'price', 'image', 'is_active'])]
class Product extends Model
{
    use HasFactory;

    // Relacionamento: Produto pertence a uma Categoria
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
