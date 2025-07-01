<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChangeRequest extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_VALIDATED = 'validated';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ON_HOLD = 'on_hold';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'client_id',
        'portal_id',
        'title',
        'description',
        'status',
        'validation_notes',
        'approval_notes',
        'developer_id',
        'validated_by',
        'deadline',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    public function portal()
    {
        return $this->belongsTo(Portal::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function developerNotes(): HasMany
    {
        return $this->hasMany(DeveloperNote::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public static function getAllStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_VALIDATED,
            self::STATUS_APPROVED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_ON_HOLD,
            self::STATUS_REJECTED,
        ];
    }
}
