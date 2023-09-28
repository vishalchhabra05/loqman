<?php

use Illuminate\Database\Seeder;

class BadgeManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bages')->insert([
            ['title'=>'one', 'price'=>'100','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
            ['title'=>'Two', 'price'=>'200','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
            ['title'=>'Three', 'price'=>'300','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
            ['title'=>'Foure', 'price'=>'400','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
            ['title'=>'Five', 'price'=>'500','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
        ]);
    }
}
