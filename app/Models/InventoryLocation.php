<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'type',
        'zone',
        'aisle',
        'shelf',
        'bin',
        'barcode',
        'capacity',
        'location_type',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function stockMovementsFrom(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'from_location_id');
    }

    public function stockMovementsTo(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'to_location_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('location_type', $type);
    }

    public function scopeInWarehouse($query)
    {
        return $query->whereHas('store', function ($q) {
            $q->where('type', 'warehouse');
        });
    }

    public function scopeInRetailStore($query)
    {
        return $query->whereHas('store', function ($q) {
            $q->where('type', 'retail');
        });
    }

    // Helper methods
    public function getFullLocationCodeAttribute(): string
    {
        $parts = [];
        
        if ($this->zone) $parts[] = $this->zone;
        if ($this->aisle) $parts[] = $this->aisle;
        if ($this->shelf) $parts[] = $this->shelf;
        if ($this->bin) $parts[] = $this->bin;
        
        return implode('-', $parts) ?: $this->name;
    }

    public function getCurrentInventory()
    {
        // Get current stock by calculating net movements
        $inbound = $this->stockMovementsTo()
            ->where('status', 'completed')
            ->sum('quantity');
            
        $outbound = $this->stockMovementsFrom()
            ->where('status', 'completed')
            ->sum('quantity');
            
        return $inbound - $outbound;
    }

    public function getCurrentInventoryValue(): float
    {
        return $this->stockMovementsTo()
            ->where('status', 'completed')
            ->whereNotNull('unit_cost')
            ->get()
            ->sum(function ($movement) {
                return $movement->quantity * $movement->unit_cost;
            });
    }

    public function getCapacityUtilizationAttribute(): float
    {
        if (!$this->capacity || $this->capacity == 0) {
            return 0;
        }
        
        return ($this->getCurrentInventory() / $this->capacity) * 100;
    }

    public function isNearCapacity(): bool
    {
        return $this->getCapacityUtilizationAttribute() > 80;
    }

    public function isOverCapacity(): bool
    {
        return $this->getCapacityUtilizationAttribute() > 100;
    }

    public function canAccommodate(float $quantity): bool
    {
        if (!$this->capacity) {
            return true; // No capacity limit
        }
        
        return ($this->getCurrentInventory() + $quantity) <= $this->capacity;
    }
}