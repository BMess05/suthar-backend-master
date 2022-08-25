<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('email', 'admin@suthar.com')->delete();
        $admin = [
            'name' => 'Admin SR',
            'email' => 'admin@suthar.com',
            'password' => bcrypt('adminsr@228'),
            'role' => 0,
            'is_active' => 1
        ];
        User::create($admin);
    }
}
