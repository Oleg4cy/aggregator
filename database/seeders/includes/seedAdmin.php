<?php

use Illuminate\Support\Facades\Hash;
use Orchid\Support\Facades\Dashboard;

function seedAdmin(): void
{
    \App\Models\User::updateOrCreate(
        ['email' => 'admin@admin.com'],
        [
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'permissions' => Dashboard::getAllowAllPermission(),
        ]
    );
}
