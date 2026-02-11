<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iteration extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Get all tasks in this batch
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
