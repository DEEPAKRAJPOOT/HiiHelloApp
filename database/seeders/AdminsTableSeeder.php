<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use App\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->delete();
        $admins = [
            'full_name' => 'Admin',
            'email' => "admin@admin.com",
            'contact_no' => '1234567890',
            'password' => Hash::make('!@#$%^@admin'),
            'permissions' => serialize(getPermissions('admin')),
            'is_active' => 'y',
            'type' => 'admin',
            'profile' => NULL,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ];

        Admin::create($admins);
    }
}
