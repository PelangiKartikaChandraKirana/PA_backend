<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Location extends Model
{
    protected $fillable = [
        'unit_id',
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'radius_meters' => 'integer',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    // Pegawai yang boleh absen di lokasi ini (override)
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_locations')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }
}