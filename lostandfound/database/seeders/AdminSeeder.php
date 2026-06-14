<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@strathmore.edu'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'System Administrator',
                'email' => 'admin@strathmore.edu',
                'password' => Hash::make('Admin@1234'),
            ]
        );
    }
}
