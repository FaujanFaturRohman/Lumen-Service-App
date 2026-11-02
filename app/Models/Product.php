<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    // created_at & updated_at diisi otomatis
    public $timestamps = true;

    protected $fillable = [
        'sku',
        'name',
        'category',
        'description',
        'price',
        'stock',
        'weight_gram',
        'is_available',
    ];

    protected $casts = [
        'price'        => 'integer',
        'stock'        => 'integer',
        'weight_gram'  => 'integer',
        'is_available' => 'boolean',
    ];
}
