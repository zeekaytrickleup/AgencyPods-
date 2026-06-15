<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoalSection extends Model
{
    protected $fillable = ['goal_id', 'type', 'content'];

    public function goal()
    {
        return $this->belongsTo(Goal::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
