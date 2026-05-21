<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'job_title',
        'office',
        'avatar_path',
        'last_active_at',
        'last_login_at',
        'password',
        'office_type',
        'office_name',
        'office_id',
        'google_id',
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
            'last_active_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the notebooks owned by the user.
     */
    public function ownedNotebooks(): HasMany
    {
        return $this->hasMany(Notebook::class, 'owner_id');
    }

    /**
     * Get the notebook memberships for the user.
     */
    public function notebookMemberships(): HasMany
    {
        return $this->hasMany(NotebookMember::class);
    }

    /**
     * Get the notebooks shared with the user.
     */
    public function sharedNotebooks(): BelongsToMany
    {
        return $this->belongsToMany(Notebook::class, 'notebook_members')
            ->withPivot(['id', 'permission', 'can_share', 'invited_by'])
            ->withTimestamps();
    }

    public function shelves(): HasMany
    {
        return $this->hasMany(Shelf::class)->orderBy('order');
    }

    /**
     * Determine if the user has one of the given roles.
     */
    public function hasRole(array|string $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($this->role, $roles, true);
    }

    /**
     * Determine if the user is an administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
