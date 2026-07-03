<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineFaultStatus extends Model
{
    protected $fillable = ['name','key','priority'];

    protected $casts = ['priority' => 'integer'];
}