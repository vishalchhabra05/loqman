<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'loqman@yopmail.com',
            'password' => Hash::make('Loqman@123'),
            'role'  =>'1',
            'number'=>'',
            'status'   =>'1',
        ]);
    }
}
