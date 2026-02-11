<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // Assuming you use Spatie

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'country_manager_id',
        'regional_manager_id',
        'state_manager_id',
        'region_id',
        'state_id',
        'territory_id',
        'sales_rep_id',
        'avatar', // if you have this
    ];

    // ... hidden and casts arrays ...

    // --- ADD THESE RELATIONSHIPS ---

    // Changed to 'regions' (Plural) and uses belongsToMany
    public function regions()
    {
        return $this->belongsToMany(Region::class, 'region_user', 'user_id', 'region_id')->withTimestamps();
    }

    public function state()
    {
        // Represents the "Area"
        return $this->belongsTo(State::class, 'state_id');
    }

    // Optional: Relationship to see who is their Regional Manager
    public function regionalManager()
    {
        return $this->belongsTo(User::class, 'regional_manager_id');
    }

    // Relationship for "Area Manager" (Boss of the Area)
    public function stateManager()
    {
        return $this->belongsTo(User::class, 'state_manager_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'user_id');
    }

    // Relationship: A User can belong to multiple States
    public function states()
    {
        // 'state_user' is the pivot table name we just created
        return $this->belongsToMany(State::class, 'state_user', 'user_id', 'state_id')->withTimestamps();
    }
}
