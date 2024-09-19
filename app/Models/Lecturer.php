<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Lecturer extends Model
{
    use Notifiable;

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }
}
