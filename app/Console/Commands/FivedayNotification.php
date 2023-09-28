<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;
use App\Model\Notificationsend;
use App\Model\Notification;

class FivedayNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fiveday:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");
     
        // $data=User::where('status','1')->whereIn('role',array('2','3'))->get();
        // $notification=Notification::where('status','1')->where('id','6')->first();
        // $notification_type="hurry";
        // $diduser="false";
        // foreach($data as $value){
        //     PushNotficationAndroid($value->fcm_token,$value->id,$notification->subject, $notification->message,$notification_type,$diduser);
        //       $notificationsend=new Notificationsend;
        //       $notificationsend->user_id=$value->id;
        //       $notificationsend->message=$notification->message;
        //       $notificationsend->status="0";
        //       $notificationsend->save();
        // }
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}
