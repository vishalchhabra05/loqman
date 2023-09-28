<?php

use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notification')->insert([
            ['title'=>"Expert & User Notification", 'subject'=>'hurry up','message'=>'Hello loqman','status'=>'1','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],

            ['title'=>"Categories", 'subject'=>'hurry','message'=>'Hello loqman','status'=>'1','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],

            ['title'=>"Call back Notification", 'subject'=>'Call back','message'=>'Expert is trying to call you, Please attend','status'=>'1','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],

            ['title'=>"Poke user Notification", 'subject'=>'Poke','message'=>'A user poked you','status'=>'1','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],

            ['title'=>"Call notification ", 'subject'=>'Seeker is trying to call you','message'=>'Seeker is trying to call you','status'=>'1','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],

            ['title'=>"Five day notification", 'subject'=>'Hello loqman','message'=>'Hello loqman','status'=>'1','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],

            ['title'=>"Online user", 'subject'=>'Expert is available now you can call the expert. ','message'=>'Expert is available now you can call the expert.','status'=>'1','created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
        ]);
    }
}
