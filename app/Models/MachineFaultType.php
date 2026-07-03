<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineFaultType extends Model
{
    protected $fillable = ['name','is_active','priority'];

    protected $casts = ['is_active' => 'boolean', 'priority' => 'integer'];

    public function machineFaults()
    {
        return $this->hasMany(MachineFault::class);
    }
}