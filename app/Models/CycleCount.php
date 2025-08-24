<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CycleCount extends Model
{
    use HasFactory;

    protected $fillable = [
        'count_number',
        'store_id',
        'location_id',
        'type',
        'status',
        'scheduled_date',
        'started_date',
        'completed_date',
        'notes',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'started_date' => 'date',
        'completed_date' => 'date',
    ];

    // Relationships
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'location_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CycleCountItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'planned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeForStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'planned')
            ->where('scheduled_date', '<', now()->toDateString());
    }

    // Helper methods
    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_date' => now()->toDateString(),
        ]);
    }

    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_date' => now()->toDateString(),
        ]);
    }

    public function getCompletionPercentageAttribute(): float
    {
        $totalItems = $this->items()->count();
        if ($totalItems === 0) return 0;
        
        $countedItems = $this->items()->where('status', '!=', 'pending')->count();
        return ($countedItems / $totalItems) * 100;
    }

    public function getTotalVarianceValueAttribute(): float
    {
        return $this->items()->sum('variance_value') ?: 0;
    }

    public function getVarianceCountAttribute(): int
    {
        return $this->items()->where('variance', '!=', 0)->count();
    }

    public function generateItems(): void
    {
        // Generate items based on count type
        switch ($this->type) {
            case 'full':
                $this->generateFullCount();
                break;
            case 'abc':
                $this->generateABCCount();
                break;
            case 'random':
                $this->generateRandomCount();
                break;
            case 'partial':
            default:
                $this->generatePartialCount();
                break;
        }
    }

    private function generateFullCount(): void
    {
        // All products and materials in store
        $products = Product::active()->get();
        $materials = Material::active()->get();
        
        foreach ($products as $product) {
            $this->createCountItem($product, Product::class);
        }
        
        foreach ($materials as $material) {
            $this->createCountItem($material, Material::class);
        }
    }

    private function generatePartialCount(): void
    {
        // Items in specific location
        if ($this->location_id) {
            $location = $this->location;
            
            // Get items that had recent movements to this location
            $recentMovements = StockMovement::where('to_location_id', $location->id)
                ->where('movement_date', '>=', now()->subDays(30))
                ->distinct()
                ->get(['item_type', 'item_id']);
                
            foreach ($recentMovements as $movement) {
                $item = $movement->item_type::find($movement->item_id);
                if ($item && $item->is_active) {
                    $this->createCountItem($item, $movement->item_type);
                }
            }
        } else {
            // Random selection of items
            $this->generateRandomCount(50);
        }
    }

    private function generateABCCount(): void
    {
        // Focus on high-value items (A category)
        $highValueProducts = Product::active()
            ->orderByDesc(\DB::raw('current_stock * retail_price'))
            ->limit(100)
            ->get();
            
        foreach ($highValueProducts as $product) {
            $this->createCountItem($product, Product::class);
        }
    }

    private function generateRandomCount($count = 25): void
    {
        // Random selection
        $products = Product::active()->inRandomOrder()->limit($count / 2)->get();
        $materials = Material::active()->inRandomOrder()->limit($count / 2)->get();
        
        foreach ($products as $product) {
            $this->createCountItem($product, Product::class);
        }
        
        foreach ($materials as $material) {
            $this->createCountItem($material, Material::class);
        }
    }

    private function createCountItem($item, $itemType): void
    {
        // Determine location - use specified location or main warehouse location
        $locationId = $this->location_id ?: 1; // Default to main warehouse
        
        CycleCountItem::create([
            'cycle_count_id' => $this->id,
            'item_type' => $itemType,
            'item_id' => $item->id,
            'location_id' => $locationId,
            'expected_quantity' => $item->current_stock,
        ]);
    }

    public static function generateCountNumber(): string
    {
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;
        
        return "CC-{$date}-" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->count_number) {
                $model->count_number = static::generateCountNumber();
            }
        });
    }
}