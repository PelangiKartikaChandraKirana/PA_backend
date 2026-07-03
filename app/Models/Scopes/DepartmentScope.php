<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class DepartmentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = Auth::user();

        // Jika user login, bukan superadmin, dan memiliki department_id (Admin OPD)
        if ($user && $user->role === 'admin' && !empty($user->department_id)) {
            $table = $model->getTable();
            
            // Jika tabel memiliki department_id langsung (seperti tabel employees)
            if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'department_id')) {
                $builder->where($table . '.department_id', $user->department_id);
            } 
            // Jika tidak, tapi memiliki relasi ke employee (seperti attendance_logs, absence_documents)
            else {
                // Kita berasumsi model ini memiliki relasi 'employee'
                $builder->whereHas('employee', function ($query) use ($user) {
                    $query->where('department_id', $user->department_id);
                });
            }
        }
    }
}
