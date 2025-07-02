<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'url',
        'ip_address',
        'description',
        'client',
        'status',
        'developer',
        'managed_by',
    ];

    /**
     * Get users associated with this portal's client
     */
    public function users()
    {
        return $this->hasMany(User::class, 'client_id', 'client');
    }
}
