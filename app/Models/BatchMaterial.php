<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'material_id',
        'quantity_required',
        'quantity_used',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'quantity_required' => 'decimal:4',
        'quantity_used' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
    ];

    // Relationships
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    // Helper methods
    public function getVarianceAttribute(): float
    {
        return $this->quantity_used - $this->quantity_required;
    }

    public function getVariancePercentageAttribute(): float
    {
        if ($this->quantity_required == 0) {
            return 0;
        }
        
        return ($this->getVarianceAttribute() / $this->quantity_required) * 100;
    }

    public function isOverUsed(): bool
    {
        return $this->quantity_used > $this->quantity_required;
    }

    public function isUnderUsed(): bool
    {
        return $this->quantity_used < $this->quantity_required;
    }

    public function getEfficiencyRateAttribute(): float
    {
        if ($this->quantity_used == 0) {
            return 0;
        }
        
        return ($this->quantity_required / $this->quantity_used) * 100;
    }
}