<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'manager_email',
        'is_active',
        'business_hours',
        'square_footage',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'business_hours' => 'array',
        'square_footage' => 'decimal:2',
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function inventoryLocations(): HasMany
    {
        return $this->hasMany(InventoryLocation::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function transfersFrom(): HasMany
    {
        return $this->hasMany(Transfer::class, 'from_store_id');
    }

    public function transfersTo(): HasMany
    {
        return $this->hasMany(Transfer::class, 'to_store_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWarehouses($query)
    {
        return $query->where('type', 'warehouse');
    }

    public function scopeRetail($query)
    {
        return $query->where('type', 'retail');
    }

    // Helper methods
    public function isWarehouse(): bool
    {
        return $this->type === 'warehouse';
    }

    public function isRetail(): bool
    {
        return $this->type === 'retail';
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address}, {$this->city}, {$this->state} {$this->zip_code}";
    }

    public function getTotalSalesAmount($startDate = null, $endDate = null)
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

    public function getInventoryValue()
    {
        return $this->inventoryLocations()
            ->with('stockMovements')
            ->get()
            ->sum(function ($location) {
                return $location->getCurrentInventoryValue();
            });
    }
}