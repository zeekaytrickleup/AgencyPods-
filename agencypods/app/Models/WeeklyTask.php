<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyTask extends Model
{
    protected $fillable = ['client_id', 'task', 'status', 'week_start'];

    protected $casts = [
        'week_start' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
