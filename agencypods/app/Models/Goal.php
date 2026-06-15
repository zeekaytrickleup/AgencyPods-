<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = ['client_id', 'title'];

    /** The four section types every goal has, in display order. */
    public const SECTION_TYPES = ['goal', 'stop', 'start', 'continue'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sections()
    {
        return $this->hasMany(GoalSection::class);
    }
}
