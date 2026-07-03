<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Position;
use App\Models\Department;
use App\Models\EmployeeType;
use App\Models\EmployeeFace;
use App\Models\Location;
use App\Models\AttendanceLog;
use App\Models\AbsenceDocument;
use App\Models\FaultReport;

use App\Models\Scopes\DepartmentScope;

class Employee extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new DepartmentScope);
    }

    protected $fillable = [
        'nip',
        'name',
        'photo',
        'position_id',
        'department_id',
        'employee_type_id',
        'status',
        'tpp_allowance',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class, 'nip', 'nip');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }

    public function faces()
    {
        return $this->hasMany(EmployeeFace::class);
    }

    public function activeFace()
    {
        return $this->hasOne(EmployeeFace::class)
            ->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ABSENSI
    |--------------------------------------------------------------------------
    */

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'employee_locations')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'employee_id');
    }

    public function absenceDocuments()
    {
        return $this->hasMany(AbsenceDocument::class, 'employee_id');
    }

    public function faultReports()
    {
        return $this->hasMany(FaultReport::class, 'employee_id');
    }
}