<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'price',
        'img',
        'stock',
        'slug',
    ];

    // Relasi Inverse: Banyak Produk dimiliki oleh 1 Kategori
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }
}