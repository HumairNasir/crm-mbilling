<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    // A State belongs to one Region
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    // (Optional) A State belongs to many Users (Area Managers/Sales Reps)
    public function users()
    {
        return $this->belongsToMany(User::class, 'state_user', 'state_id', 'user_id');
    }
}
