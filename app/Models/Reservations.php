<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservations extends Model
{
    use SoftDeletes, HasFactory;
    public $timestamps = true;
    public function doctor_details()
    {
        return $this->hasMany(Doctors::class, 'id', 'doctor_id')->where('is_active', 1);
    }
    public function pateint_details()
    {
        return $this->hasMany(Doctors::class, 'id', 'pateint_id')->where('is_active', 1);
    }
    public function otRoom_details()
    {
        return $this->hasMany(OperationTheatres::class, 'id', 'room_id')->where('is_active', 1);
    }
}