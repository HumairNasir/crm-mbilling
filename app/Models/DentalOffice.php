<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DentalOffice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'sales_rep_id',
        'country',
        'region_id',
        'state_id',
        'territory_id',
        'contacted_source',
        'receptive',
        'purchase_product',
        'follow_up_date',
        'contact_date',
        'contact_person',
        'description',
    ];

    public function salesRep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function region()
    {
        return $this->belongsTo(\App\Models\Region::class, 'region_id');
    }

    public function state()
    {
        return $this->belongsTo(\App\Models\State::class, 'state_id');
    }

    public function territory()
    {
        return $this->belongsTo(\App\Models\Territory::class, 'territory_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
