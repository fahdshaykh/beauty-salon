<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
class SmsCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:cron';

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
        \Log::info("Cron is working fine!");
     
        $settings = DB::table('companies')->join('company_settings','companies.id','=','company_settings.company_id')->select('company_settings.company_id','company_settings.site_name','company_settings.sms_limit','company_settings.sms_count')->where('companies.is_active', '1')->get();
        $date = date("d-M-Y h:i:a");
        if(!$settings->isEmpty()){
            foreach($settings as $set){
                $totalsms = $set->sms_count + $set->sms_limit;
                DB::table('company_settings')->where('company_id', $set->company_id)->update([
                    'sms_count' => $totalsms,
                ]);
                \Log::info($totalsms. ' Sms Added to company '.$set->site_name.' on date '.$date);
                
            }
        }
        else{
            \Log::info('There is issue in running command '.$date );
           
        }
       
      
        
    }
}
