<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_number',
        'product_id',
        'quantity_planned',
        'quantity_produced',
        'quantity_quality_passed',
        'quantity_defective',
        'status',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'estimated_cost',
        'actual_cost',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'datetime',
        'actual_end_date' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function batchMaterials(): HasMany
    {
        return $this->hasMany(BatchMaterial::class);
    }

    public function productionStages(): HasMany
    {
        return $this->hasMany(BatchProductionStage::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planned', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'in_progress')
            ->where('planned_end_date', '<', now()->toDateString());
    }

    // Helper methods
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->quantity_planned == 0) {
            return 0;
        }
        
        return ($this->quantity_produced / $this->quantity_planned) * 100;
    }

    public function getQualityPassRateAttribute(): float
    {
        if ($this->quantity_produced == 0) {
            return 0;
        }
        
        return ($this->quantity_quality_passed / $this->quantity_produced) * 100;
    }

    public function getDefectRateAttribute(): float
    {
        if ($this->quantity_produced == 0) {
            return 0;
        }
        
        return ($this->quantity_defective / $this->quantity_produced) * 100;
    }

    public function isOverdue(): bool
    {
        return $this->status === 'in_progress' && 
               $this->planned_end_date < now()->toDateString();
    }

    public function getDaysRemainingAttribute(): int
    {
        if ($this->status === 'completed') {
            return 0;
        }
        
        return now()->diffInDays($this->planned_end_date, false);
    }

    public function getActualDurationAttribute(): ?int
    {
        if (!$this->actual_start_date || !$this->actual_end_date) {
            return null;
        }
        
        return $this->actual_start_date->diffInDays($this->actual_end_date);
    }

    public function getPlannedDurationAttribute(): int
    {
        return $this->planned_start_date->diffInDays($this->planned_end_date);
    }

    public function getTotalMaterialCost(): float
    {
        return $this->batchMaterials()->sum('total_cost');
    }

    public function getTotalLaborCost(): float
    {
        return $this->productionStages()->sum('labor_cost');
    }

    public function getUnitCostAttribute(): float
    {
        if ($this->quantity_produced == 0) {
            return 0;
        }
        
        $totalCost = $this->getTotalMaterialCost() + $this->getTotalLaborCost();
        return $totalCost / $this->quantity_produced;
    }

    public function startProduction(): void
    {
        $this->update([
            'status' => 'in_progress',
            'actual_start_date' => now(),
        ]);
    }

    public function completeProduction(): void
    {
        $this->update([
            'status' => 'completed',
            'actual_end_date' => now(),
            'actual_cost' => $this->getTotalMaterialCost() + $this->getTotalLaborCost(),
        ]);

        // Update product stock
        $this->product->increment('current_stock', $this->quantity_quality_passed);
        
        // Create stock movement
        StockMovement::create([
            'item_type' => Product::class,
            'item_id' => $this->product_id,
            'batch_id' => $this->id,
            'quantity' => $this->quantity_quality_passed,
            'type' => 'production_output',
            'movement_date' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    public static function generateBatchNumber($productId): string
    {
        $product = Product::find($productId);
        $date = now()->format('Ymd');
        $sequence = static::where('batch_number', 'like', "{$product->sku}-{$date}-%")->count() + 1;
        
        return "{$product->sku}-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}