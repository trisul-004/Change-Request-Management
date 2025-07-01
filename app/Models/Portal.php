<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'ip_address',
        'description',
        'client',
        'status',
        'developer',
    ];

    /**
     * Get users associated with this portal's client
     */
    public function users()
    {
        return $this->hasMany(User::class, 'client_id', 'client');
    }
}
