<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'customer_name', 'product_name', 'quantity',
        'total_price', 'order_date', 'status',
    ];
}