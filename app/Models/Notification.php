<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'priority',
        'channels',
        'read_at',
        'sent_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'channels' => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    // Helper methods
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }

    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    // Static factory methods
    public static function createLowStockAlert(Product $product, User $user): self
    {
        return static::create([
            'user_id' => $user->id,
            'type' => 'low_stock',
            'title' => 'Low Stock Alert',
            'message' => "{$product->name} is low on stock ({$product->current_stock} remaining)",
            'data' => [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->current_stock,
                'minimum_stock' => $product->minimum_stock,
            ],
            'priority' => 'medium',
            'channels' => ['database', 'push'],
        ]);
    }

    public static function createTransferApprovalRequest(Transfer $transfer, User $approver): self
    {
        return static::create([
            'user_id' => $approver->id,
            'type' => 'transfer_approval',
            'title' => 'Transfer Approval Required',
            'message' => "Transfer {$transfer->transfer_number} requires your approval",
            'data' => [
                'transfer_id' => $transfer->id,
                'transfer_number' => $transfer->transfer_number,
                'from_store' => $transfer->fromStore->name,
                'to_store' => $transfer->toStore->name,
                'product' => $transfer->product->name,
                'quantity' => $transfer->quantity_requested,
            ],
            'priority' => 'high',
            'channels' => ['database', 'push', 'email'],
        ]);
    }

    public static function createBatchCompletionNotice(Batch $batch, User $user): self
    {
        return static::create([
            'user_id' => $user->id,
            'type' => 'batch_completed',
            'title' => 'Batch Production Complete',
            'message' => "Batch {$batch->batch_number} has been completed",
            'data' => [
                'batch_id' => $batch->id,
                'batch_number' => $batch->batch_number,
                'product_name' => $batch->product->name,
                'quantity_produced' => $batch->quantity_produced,
                'quality_pass_rate' => $batch->quality_pass_rate,
            ],
            'priority' => 'medium',
            'channels' => ['database', 'push'],
        ]);
    }

    public static function createCycleCountReminder(CycleCount $cycleCount, User $user): self
    {
        return static::create([
            'user_id' => $user->id,
            'type' => 'cycle_count_reminder',
            'title' => 'Cycle Count Scheduled',
            'message' => "Cycle count {$cycleCount->count_number} is scheduled for today",
            'data' => [
                'cycle_count_id' => $cycleCount->id,
                'count_number' => $cycleCount->count_number,
                'type' => $cycleCount->type,
                'scheduled_date' => $cycleCount->scheduled_date->toDateString(),
            ],
            'priority' => 'medium',
            'channels' => ['database', 'push'],
        ]);
    }

    public static function createSystemAlert(string $title, string $message, array $data = []): self
    {
        // Send to all admin users
        $adminUsers = User::role('admin')->get();
        $notifications = [];

        foreach ($adminUsers as $user) {
            $notifications[] = static::create([
                'user_id' => $user->id,
                'type' => 'system_alert',
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'priority' => 'high',
                'channels' => ['database', 'push', 'email'],
            ]);
        }

        return $notifications[0] ?? null;
    }
}