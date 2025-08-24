<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'type',
        'description',
        'size',
        'color',
        'fit',
        'retail_price',
        'wholesale_price',
        'cost_price',
        'current_stock',
        'minimum_stock',
        'barcode',
        'images',
        'is_active',
    ];

    protected $casts = [
        'retail_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(Transfer::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'item');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBySize($query, $size)
    {
        return $query->where('size', $size);
    }

    public function scopeByColor($query, $color)
    {
        return $query->where('color', $color);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }

    // Helper methods
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function getStockStatusAttribute(): string
    {
        return $this->isLowStock() ? 'low' : 'normal';
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} - {$this->color} - {$this->size} - {$this->fit}";
    }

    public function getMarginAttribute(): float
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return 0;
        }
        
        return (($this->retail_price - $this->cost_price) / $this->retail_price) * 100;
    }

    public function getMarkupAttribute(): float
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return 0;
        }
        
        return (($this->retail_price - $this->cost_price) / $this->cost_price) * 100;
    }

    public function getInventoryValue(): float
    {
        return $this->current_stock * ($this->cost_price ?: $this->retail_price);
    }

    public function getTotalSalesAmount($startDate = null, $endDate = null): float
    {
        $query = $this->sales();
        
        if ($startDate) {
            $query->where('sale_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('sale_date', '<=', $endDate);
        }
        
        return $query->sum('final_amount');
    }

    public function getTotalSalesQuantity($startDate = null, $endDate = null): int
    {
        $query = $this->sales();
        
        if ($startDate) {
            $query->where('sale_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('sale_date', '<=', $endDate);
        }
        
        return $query->sum('quantity');
    }

    public function calculateSalesVelocity(int $days = 30): float
    {
        $sales = $this->getTotalSalesQuantity(
            now()->subDays($days),
            now()
        );
        
        return $sales / $days;
    }

    public function getReorderPointAttribute(): int
    {
        $velocity = $this->calculateSalesVelocity();
        $leadTime = 7; // Default 7 days for production
        return ceil($velocity * $leadTime * 1.2); // 20% safety stock
    }

    public function adjustStock(int $quantity, string $reason = null): void
    {
        $this->increment('current_stock', $quantity);
        
        StockMovement::create([
            'item_type' => self::class,
            'item_id' => $this->id,
            'quantity' => $quantity,
            'type' => 'adjustment',
            'reason' => $reason,
            'movement_date' => now(),
            'created_by' => auth()->id(),
        ]);
    }
}