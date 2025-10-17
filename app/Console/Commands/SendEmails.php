<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Mail;
class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendemails:cron';

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
     * @return mixed
     */
    public function handle()
    {
        
     
        //$date = date("Y-m-d h:i A");
        $scheduled_emails = DB::table('today_scheduled_emails')->get();
        
        if(!$scheduled_emails->isEmpty()){
            $ids =  array();
            foreach($scheduled_emails as $scheduled){
                $dbtime =  strtotime($scheduled->schedule_time);
                $current_time = strtotime(date("h:i A"));
                \Log::info('Db Time: '.$dbtime.'>>>>>>>>'.$current_time);
                $template_text =  DB::table('templates')->where('id', $scheduled->template_id)->select('title','description')->get();
                $data['description'] = $template_text[0]->description;
                $title = $template_text[0]->title;
                $subject = $template_text[0]->title;
                if($dbtime<=$current_time){
                    $email = $scheduled->email;
                    Mail::send(['html' => 'emails.template'], $data, function($message) use ($email,$subject,$title) {
                        $message->to($email)->subject($subject);
                        $message->from(Auth::user()->companySetting->email ?? 'noreply@example.com', $title);
                        // $message->from('noreply@bSaloon.co.uk',$title);
                    });
                   //$emails[] = $scheduled->email;
                    $ids[] = $scheduled->id; 
                }
            }
            if(sizeof($ids)>0){
               
                if (Mail::failures()){
                    $failures = Mail::failures();
                    \Log::info($failures);
                }
                else{
                    DB::table('today_scheduled_emails')->whereIn('id', $ids)->delete();
                    \Log::info($ids);
                }
               
            }
        }
        //\Log::info('>>>>>>>>'.date("h:i A"));
    }
}
