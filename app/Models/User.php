<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'client_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teamMembers()
    {
        return $this->hasMany(TeamAssignment::class, 'supervisor_id');
    }

    public function assignedDevelopers()
    {
        return $this->hasManyThrough(
            User::class,
            TeamAssignment::class,
            'supervisor_id',
            'id',
            'id',
            'developer_id'
        );
    }

    public function supervisor()
    {
        return $this->hasOne(TeamAssignment::class, 'developer_id');
    }

    /**
     * Get portals associated with this user's client
     */
    public function portals()
    {
        return $this->hasMany(Portal::class, 'client', 'client_id');
    }

    /**
     * Get available portals for this user's client
     */
    public function getAvailablePortals()
    {
        if ($this->hasRole('admin')) {
            return Portal::all();
        }
        
        return $this->portals;
    }

    public function canAccessPanel(): bool
    {
        return $this->hasRole('admin');
    }
}
