<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getBachelorAttribute($value)
    {

        return json_decode($value, true);
    }

    public function getMasterAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setBachelorAttribute($value)
    {
        $value = json_encode($value);

        $this->attributes['bachelor'] = $value;

        return $this;
    }

    public function setMasterAttribute($value)
    {
        $value = json_encode($value);

        $this->attributes['master'] = $value;

        return $this;
    }
}
