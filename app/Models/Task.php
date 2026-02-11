<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = []; // Allows mass assignment

    // Link to the Sales Rep
    public function salesRep()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Link to the Dental Office (The Lead)
    public function dentalOffice()
    {
        return $this->belongsTo(DentalOffice::class, 'dental_office_id');
    }

    // Link to the Batch/Iteration
    public function iteration()
    {
        return $this->belongsTo(Iteration::class);
    }
}
