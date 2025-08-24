<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'item_type',
        'item_id',
        'batch_id',
        'from_location_id',
        'to_location_id',
        'quantity',
        'unit_cost',
        'type',
        'status',
        'reason',
        'notes',
        'created_by',
        'movement_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_cost' => 'decimal:4',
        'movement_date' => 'datetime',
    ];

    // Relationships
    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'to_location_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForLocation($query, $locationId)
    {
        return $query->where(function ($q) use ($locationId) {
            $q->where('from_location_id', $locationId)
              ->orWhere('to_location_id', $locationId);
        });
    }

    public function scopeInbound($query)
    {
        return $query->whereIn('type', ['receipt', 'transfer', 'production_output', 'return']);
    }

    public function scopeOutbound($query)
    {
        return $query->whereIn('type', ['shipment', 'transfer', 'production_input', 'sale']);
    }

    // Helper methods
    public function getTotalValueAttribute(): float
    {
        return $this->quantity * ($this->unit_cost ?: 0);
    }

    public function isInbound(): bool
    {
        return in_array($this->type, ['receipt', 'transfer', 'production_output', 'return']);
    }

    public function isOutbound(): bool
    {
        return in_array($this->type, ['shipment', 'transfer', 'production_input', 'sale']);
    }

    public function getDirectionAttribute(): string
    {
        return $this->isInbound() ? 'in' : 'out';
    }

    public function getMovementDescriptionAttribute(): string
    {
        $item = $this->item;
        $itemName = $item ? $item->name : 'Unknown Item';
        
        switch ($this->type) {
            case 'receipt':
                return "Received {$this->quantity} {$itemName}";
            case 'shipment':
                return "Shipped {$this->quantity} {$itemName}";
            case 'transfer':
                return "Transferred {$this->quantity} {$itemName}";
            case 'adjustment':
                return "Adjusted {$this->quantity} {$itemName}";
            case 'production_input':
                return "Used {$this->quantity} {$itemName} in production";
            case 'production_output':
                return "Produced {$this->quantity} {$itemName}";
            case 'sale':
                return "Sold {$this->quantity} {$itemName}";
            case 'return':
                return "Returned {$this->quantity} {$itemName}";
            default:
                return "Moved {$this->quantity} {$itemName}";
        }
    }

    public static function generateReferenceNumber($type): string
    {
        $prefix = strtoupper(substr($type, 0, 3));
        $date = now()->format('Ymd');
        $sequence = static::where('reference_number', 'like', "{$prefix}-{$date}-%")->count() + 1;
        
        return "{$prefix}-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->reference_number) {
                $model->reference_number = static::generateReferenceNumber($model->type);
            }
        });
    }
}