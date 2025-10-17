<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class ScheduledEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduledemail:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command wil run daily to check todays scheduled emails';

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
       
        $date = date("Y-m-d");
        $scheduled_emails = DB::table('scheduled_emails')->get();
        $ids =  array();
        if(!$scheduled_emails->isEmpty()){
            
            foreach($scheduled_emails as $scheduled){
                //\Log::info($scheduled->scheduled_date.'>>>>>'.$date);
                if(strtotime($scheduled->schedule_date)<=strtotime($date)){
                  
                    $data = array(
                        'email'=> $scheduled->email,
                        'template_id' => $scheduled->template_id,
                        'schedule_date' => $scheduled->schedule_date,
                        'schedule_time' =>$scheduled->schedule_time,
                        'company_id' => $scheduled->company_id
    
                    );
                    DB::table('today_scheduled_emails')->insert($data);
                    $ids[] = $scheduled->id; 
                }
            }
            if(sizeof($ids)>0){
                DB::table('scheduled_emails')->whereIn('id', $ids)->delete();
            }
        }
        \Log::info($ids);
        //$this->info('Demo:Cron Cummand Run successfully!');
    }
}
