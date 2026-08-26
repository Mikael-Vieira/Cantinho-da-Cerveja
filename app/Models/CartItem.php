<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    // Relacionamento com o Produto (para conseguirmos puxar o nome, preço e imagem)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relacionamento com o Usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
