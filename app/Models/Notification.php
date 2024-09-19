<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }
}