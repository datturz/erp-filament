<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_number',
        'from_store_id',
        'to_store_id', 
        'product_id',
        'batch_id',
        'quantity_requested',
        'quantity_shipped',
        'quantity_received',
        'status',
        'requested_date',
        'shipped_date',
        'received_date',
        'reason',
        'notes',
        'requested_by',
        'approved_by',
        'shipped_by',
        'received_by',
    ];

    protected $casts = [
        'requested_date' => 'date',
        'shipped_date' => 'date', 
        'received_date' => 'date',
    ];

    // Relationships
    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function shippedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shipped_by');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'requested');
    }

    public function scopeInTransit($query)
    {
        return $query->where('status', 'shipped');
    }

    public function scopeForStore($query, $storeId)
    {
        return $query->where(function ($q) use ($storeId) {
            $q->where('from_store_id', $storeId)
              ->orWhere('to_store_id', $storeId);
        });
    }

    // Helper methods
    public function approve(User $user): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $user->id,
        ]);
    }

    public function ship(User $user, int $quantityShipped): void
    {
        $this->update([
            'status' => 'shipped',
            'quantity_shipped' => $quantityShipped,
            'shipped_date' => now()->toDateString(),
            'shipped_by' => $user->id,
        ]);

        // Reduce stock at source
        $this->product->decrement('current_stock', $quantityShipped);
        
        // Create stock movement
        StockMovement::create([
            'item_type' => Product::class,
            'item_id' => $this->product_id,
            'quantity' => -$quantityShipped,
            'type' => 'transfer',
            'movement_date' => now(),
            'created_by' => $user->id,
        ]);
    }

    public function receive(User $user, int $quantityReceived): void
    {
        $this->update([
            'status' => 'received',
            'quantity_received' => $quantityReceived,
            'received_date' => now()->toDateString(),
            'received_by' => $user->id,
        ]);

        // Add stock at destination
        $this->product->increment('current_stock', $quantityReceived);
        
        // Create stock movement
        StockMovement::create([
            'item_type' => Product::class,
            'item_id' => $this->product_id,
            'quantity' => $quantityReceived,
            'type' => 'transfer',
            'movement_date' => now(),
            'created_by' => $user->id,
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $reason,
        ]);
    }

    public function getVarianceAttribute(): int
    {
        return ($this->quantity_shipped ?: 0) - ($this->quantity_received ?: 0);
    }

    public function getDaysInTransitAttribute(): ?int
    {
        if (!$this->shipped_date) return null;
        
        $endDate = $this->received_date ?: now()->toDateString();
        return $this->shipped_date->diffInDays($endDate);
    }

    public static function generateTransferNumber(): string
    {
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;
        
        return "TRF-{$date}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->transfer_number) {
                $model->transfer_number = static::generateTransferNumber();
            }
            
            if (!$model->requested_date) {
                $model->requested_date = now()->toDateString();
            }
        });
    }
}