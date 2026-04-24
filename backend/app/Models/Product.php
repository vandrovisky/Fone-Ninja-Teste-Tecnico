<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sale_price',
        'average_cost',
        'current_stock',
    ];

    protected $casts = [
        'sale_price' => 'decimal:2',
        'average_cost' => 'decimal:2',
        'current_stock' => 'integer',
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
