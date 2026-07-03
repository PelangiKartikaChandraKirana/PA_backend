<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['username']) || empty($row['name']) || empty($row['role'])) {
            return null;
        }

        return new User([
            'name' => $row['name'],
            'username' => $row['username'],
            'email' => $row['email'] ?? null,
            'role' => $row['role'],
            'password' => Hash::make($row['password'] ?? 'password123'),
            'is_active' => isset($row['is_active']) ? (bool) $row['is_active'] : true,
        ]);
    }
}