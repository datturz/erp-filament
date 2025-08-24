<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CycleCountItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cycle_count_id',
        'item_type',
        'item_id',
        'location_id',
        'expected_quantity',
        'counted_quantity',
        'variance',
        'variance_value',
        'status',
        'notes',
        'counted_by',
        'counted_at',
    ];

    protected $casts = [
        'expected_quantity' => 'decimal:4',
        'counted_quantity' => 'decimal:4',
        'variance' => 'decimal:4',
        'variance_value' => 'decimal:2',
        'counted_at' => 'datetime',
    ];

    // Relationships
    public function cycleCount(): BelongsTo
    {
        return $this->belongsTo(CycleCount::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    public function countedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counted_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCounted($query)
    {
        return $query->where('status', '!=', 'pending');
    }

    public function scopeWithVariance($query)
    {
        return $query->whereRaw('ABS(variance) > 0.01');
    }

    // Helper methods
    public function recordCount(float $countedQuantity, User $user, string $notes = null): void
    {
        $this->counted_quantity = $countedQuantity;
        $this->variance = $countedQuantity - $this->expected_quantity;
        $this->counted_by = $user->id;
        $this->counted_at = now();
        $this->status = 'counted';
        $this->notes = $notes;
        
        // Calculate variance value
        $item = $this->item;
        if ($item) {
            $unitValue = $item instanceof Product 
                ? ($item->cost_price ?: $item->retail_price)
                : $item->unit_cost;
            $this->variance_value = $this->variance * $unitValue;
        }
        
        $this->save();
    }

    public function adjustInventory(User $user): void
    {
        if ($this->status !== 'counted' || $this->variance == 0) {
            return;
        }
        
        // Update item stock
        $item = $this->item;
        $item->adjustStock($this->variance, "Cycle count adjustment - {$this->cycleCount->count_number}");
        
        // Mark as adjusted
        $this->update(['status' => 'adjusted']);
        
        // Create stock movement record
        StockMovement::create([
            'item_type' => $this->item_type,
            'item_id' => $this->item_id,
            'quantity' => $this->variance,
            'type' => 'adjustment',
            'reason' => "Cycle count adjustment - {$this->cycleCount->count_number}",
            'movement_date' => now(),
            'created_by' => $user->id,
        ]);
    }

    public function verify(): void
    {
        $this->update(['status' => 'verified']);
    }

    public function hasVariance(): bool
    {
        return abs($this->variance ?? 0) > 0.01;
    }

    public function getVariancePercentageAttribute(): float
    {
        if ($this->expected_quantity == 0) {
            return $this->counted_quantity > 0 ? 100 : 0;
        }
        
        return ($this->variance / $this->expected_quantity) * 100;
    }

    public function getAccuracyRateAttribute(): float
    {
        if ($this->expected_quantity == 0) {
            return $this->counted_quantity == 0 ? 100 : 0;
        }
        
        $accuracyRate = 100 - abs($this->variance_percentage);
        return max(0, $accuracyRate);
    }

    public function getItemNameAttribute(): string
    {
        return $this->item ? $this->item->name : 'Unknown Item';
    }

    public function getItemSkuAttribute(): string
    {
        return $this->item ? $this->item->sku : 'Unknown SKU';
    }
}