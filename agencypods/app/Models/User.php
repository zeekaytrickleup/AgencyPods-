<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'manager_id',
    ];

    /**
     * Pods this user manages (a manager can own several pods).
     */
    public function pods()
    {
        return $this->hasMany(Pod::class, 'manager_id');
    }

    /** The manager a team member reports to. */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /** Team members reporting to this manager. */
    public function teamMembers()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isPodManager(): bool
    {
        return $this->role === 'pod_manager';
    }

    public function isTeamMember(): bool
    {
        return $this->role === 'team_member';
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            'super_admin' => 'Super Admin',
            'pod_manager' => 'Pod Manager',
            'team_member' => 'Team Member',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

    /** Can this user see / work in the given pod? */
    public function canAccessPod(Pod $pod): bool
    {
        return $this->isSuperAdmin()
            || ($this->isPodManager() && $pod->manager_id === $this->id)
            || ($this->isTeamMember() && $this->manager_id !== null && $pod->manager_id === $this->manager_id);
    }

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
}
