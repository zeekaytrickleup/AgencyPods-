<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Pod extends Model
{
    protected $fillable = ['name', 'color', 'manager_id'];

    /**
     * Limit pods to those a user may see: super admins see all,
     * pod managers see only the pods they manage.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        if ($user->isTeamMember()) {
            // Team members see the pods their manager owns.
            return $query->where('manager_id', $user->manager_id);
        }

        // Pod managers see the pods they own.
        return $query->where('manager_id', $user->id);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}
