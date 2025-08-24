<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'employee_id',
        'store_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function createdBatches()
    {
        return $this->hasMany(Batch::class, 'created_by');
    }

    public function processedSales()
    {
        return $this->hasMany(Sale::class, 'processed_by');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    // Helper methods
    public function isWarehouseManager(): bool
    {
        return $this->hasRole('warehouse_manager') || $this->hasRole('admin');
    }

    public function isStoreManager(): bool
    {
        return $this->hasRole('store_manager') || $this->hasRole('admin');
    }

    public function canAccessStore(Store $store): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        return $this->store_id === $store->id;
    }

    public function canManageProduction(): bool
    {
        return $this->hasAnyRole(['admin', 'warehouse_manager', 'production_supervisor']);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name . ($this->employee_id ? " ({$this->employee_id})" : '');
    }
}