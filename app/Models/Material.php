<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'type',
        'description',
        'color',
        'size',
        'unit_of_measure',
        'unit_cost',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'supplier_name',
        'supplier_sku',
        'lead_time_days',
        'is_active',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function batchMaterials(): HasMany
    {
        return $this->hasMany(BatchMaterial::class);
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

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }

    // Helper methods
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function isOverStock(): bool
    {
        return $this->maximum_stock && $this->current_stock > $this->maximum_stock;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->isLowStock()) {
            return 'low';
        }
        
        if ($this->isOverStock()) {
            return 'overstock';
        }
        
        return 'normal';
    }

    public function getFullNameAttribute(): string
    {
        $parts = [$this->name];
        
        if ($this->color) {
            $parts[] = $this->color;
        }
        
        if ($this->size) {
            $parts[] = $this->size;
        }
        
        return implode(' - ', $parts);
    }

    public function getInventoryValue(): float
    {
        return $this->current_stock * $this->unit_cost;
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

    public function calculateUsageVelocity(int $days = 30): float
    {
        $usage = $this->stockMovements()
            ->where('type', 'production_input')
            ->where('movement_date', '>=', now()->subDays($days))
            ->sum('quantity');
            
        return $usage / $days;
    }

    public function getReorderPointAttribute(): int
    {
        $velocity = $this->calculateUsageVelocity();
        return ceil($velocity * $this->lead_time_days * 1.2); // 20% safety stock
    }
}