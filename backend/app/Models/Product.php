<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'shop_id',
        'category_id',
        'sku',
        'stock_quantity',
        'images',
        'weight',
        'dimensions',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'weight' => 'decimal:2',
            'images' => 'array',
            'dimensions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function decreaseStock(int $quantity): void
    {
        $this->stock_quantity = max(0, $this->stock_quantity - $quantity);
        $this->save();
    }

    public function increaseStock(int $quantity): void
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }
}