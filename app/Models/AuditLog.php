<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event',
        'auditable_type',
        'auditable_id',
        'user_id',
        'user_type',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
        'additional_data',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'additional_data' => 'array',
    ];

    // Relationships
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByEvent($query, $event)
    {
        return $query->where('event', $event);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByModel($query, $modelType, $modelId = null)
    {
        $query = $query->where('auditable_type', $modelType);
        
        if ($modelId) {
            $query->where('auditable_id', $modelId);
        }
        
        return $query;
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getChangedAttributesAttribute(): array
    {
        if (!$this->old_values || !$this->new_values) {
            return [];
        }

        $changed = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changed[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changed;
    }

    public function getModelNameAttribute(): string
    {
        return class_basename($this->auditable_type);
    }

    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'System';
    }

    // Static methods for logging
    public static function logCreate($model, $user = null, $additionalData = []): self
    {
        return static::create([
            'event' => 'created',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'user_id' => $user ? $user->id : auth()->id(),
            'new_values' => $model->getAttributes(),
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional_data' => $additionalData,
        ]);
    }

    public static function logUpdate($model, $originalValues, $user = null, $additionalData = []): self
    {
        return static::create([
            'event' => 'updated',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'user_id' => $user ? $user->id : auth()->id(),
            'old_values' => $originalValues,
            'new_values' => $model->getAttributes(),
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional_data' => $additionalData,
        ]);
    }

    public static function logDelete($model, $user = null, $additionalData = []): self
    {
        return static::create([
            'event' => 'deleted',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'user_id' => $user ? $user->id : auth()->id(),
            'old_values' => $model->getAttributes(),
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional_data' => $additionalData,
        ]);
    }

    public static function logAccess($model, $user = null, $additionalData = []): self
    {
        return static::create([
            'event' => 'accessed',
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'user_id' => $user ? $user->id : auth()->id(),
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional_data' => $additionalData,
        ]);
    }

    public static function logCustomEvent($event, $model, $data = [], $user = null): self
    {
        return static::create([
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'user_id' => $user ? $user->id : auth()->id(),
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional_data' => $data,
        ]);
    }

    // Security audit methods
    public static function logSecurityEvent($event, $data = [], $user = null): self
    {
        return static::create([
            'event' => "security.{$event}",
            'auditable_type' => User::class,
            'auditable_id' => $user ? $user->id : auth()->id(),
            'user_id' => $user ? $user->id : auth()->id(),
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'additional_data' => $data,
        ]);
    }

    public static function logLoginAttempt($user, $successful = true): self
    {
        return static::logSecurityEvent($successful ? 'login_success' : 'login_failed', [
            'email' => $user ? $user->email : request()->input('email'),
            'successful' => $successful,
        ], $user);
    }

    public static function logPasswordChange($user): self
    {
        return static::logSecurityEvent('password_changed', [], $user);
    }

    public static function logPermissionChange($user, $oldRoles, $newRoles): self
    {
        return static::logSecurityEvent('permissions_changed', [
            'old_roles' => $oldRoles,
            'new_roles' => $newRoles,
        ], $user);
    }
}