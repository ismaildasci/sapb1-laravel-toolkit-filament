<?php

declare(strict_types=1);

namespace SapB1\Toolkit\Filament\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $entity
 * @property string $sync_type
 * @property string $status
 * @property int $records_synced
 * @property int $records_created
 * @property int $records_updated
 * @property int $records_deleted
 * @property int|null $duration_ms
 * @property string|null $error_message
 * @property array<string, mixed>|null $metadata
 * @property string|null $tenant_id
 * @property Carbon $started_at
 * @property Carbon|null $completed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SyncHistory extends Model
{
    protected $table = 'sap_sync_history';

    protected $fillable = [
        'entity',
        'sync_type',
        'status',
        'records_synced',
        'records_created',
        'records_updated',
        'records_deleted',
        'duration_ms',
        'error_message',
        'metadata',
        'tenant_id',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'records_synced' => 'integer',
        'records_created' => 'integer',
        'records_updated' => 'integer',
        'records_deleted' => 'integer',
        'duration_ms' => 'integer',
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Sync types
    public const TYPE_INCREMENTAL = 'incremental';

    public const TYPE_FULL = 'full';

    public const TYPE_FULL_WITH_DELETES = 'full_with_deletes';

    // Status constants
    public const STATUS_RUNNING = 'running';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    /**
     * Start a new sync history record.
     */
    public static function start(string $entity, string $syncType = self::TYPE_INCREMENTAL, ?string $tenantId = null): self
    {
        return static::create([
            'entity' => $entity,
            'sync_type' => $syncType,
            'status' => self::STATUS_RUNNING,
            'started_at' => now(),
            'tenant_id' => $tenantId,
        ]);
    }

    /**
     * Mark sync as completed.
     */
    public function complete(int $synced = 0, int $created = 0, int $updated = 0, int $deleted = 0): self
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'records_synced' => $synced,
            'records_created' => $created,
            'records_updated' => $updated,
            'records_deleted' => $deleted,
            'duration_ms' => $this->started_at->diffInMilliseconds(now()),
            'completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark sync as failed.
     */
    public function fail(string $errorMessage): self
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'duration_ms' => $this->started_at->diffInMilliseconds(now()),
            'completed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Scope for entity.
     */
    public function scopeForEntity($query, string $entity)
    {
        return $query->where('entity', $entity);
    }

    /**
     * Scope for status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for tenant.
     */
    public function scopeForTenant($query, ?string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Get duration in human-readable format.
     */
    public function getDurationAttribute(): ?string
    {
        if ($this->duration_ms === null) {
            return null;
        }

        if ($this->duration_ms < 1000) {
            return $this->duration_ms.'ms';
        }

        $seconds = $this->duration_ms / 1000;

        if ($seconds < 60) {
            return round($seconds, 1).'s';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        return $minutes.'m '.round($remainingSeconds).'s';
    }
}
