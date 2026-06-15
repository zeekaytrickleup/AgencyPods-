<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'goal_section_id',
        'original_name',
        'stored_path',
        'size',
        'mime',
        'file_type',
        'uploaded_by',
    ];

    /** Human-readable size, e.g. "2.1 MB" / "340 KB". */
    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;

        if ($bytes >= 1048576) {
            return rtrim(rtrim(number_format($bytes / 1048576, 1), '0'), '.').' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024).' KB';
        }

        return $bytes.' B';
    }

    public function section()
    {
        return $this->belongsTo(GoalSection::class, 'goal_section_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
