<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['pod_id', 'name', 'industry'];

    public function pod()
    {
        return $this->belongsTo(Pod::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function weeklyTasks()
    {
        return $this->hasMany(WeeklyTask::class);
    }
}
