<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Templates;
use Str;
use Mail;
use DB;
use Illuminate\Support\Facades\Auth;
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Variable;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\Helpers\Builder\Attachment;
use MailerSend\Exceptions\MailerSendValidationException;
//use App\Mail\InvoiceMail;
class PowerMailingController extends Controller
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
  
    public function create()
    {
        $services=DB::table('services')->select('id','name')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->groupBy('name')->get();
        // for zip-code
        $templates_data =Templates::select('*')->where('type', 3)->where('status', 1)->where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->get();
        $zipcode=DB::table('customers')->select('zip')->where('company_id', auth()->user()->company_id)->groupBy('zip')->get();
        $customer_detail = DB::table('customers')->where('company_id', auth()->user()->company_id)->where('optout', '!=',1)->where('email','!=','')->where('email','!=', NULL)->get();
        return view('power_mailing.create',compact('customer_detail','zipcode','services','templates_data'));
    }

    public function get_template_preview($template_id){
        $template = Templates::select('description')->where('id',$template_id)->first();
        echo $template->description; exit;
    }

    public function email_filter(Request $request){
        $data = $request->all();
        $templates_data =Templates::select('*')->where('type', 3)->where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->get();
        $services=DB::table('services')->select('id','name')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->groupBy('name')->get();
        // for zip-code
        $zipcode=DB::table('customers')->select('zip')->where('optout', '!=',1)->where('company_id', auth()->user()->company_id)->groupBy('zip')->get();
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $service_id=$request->service_id;
        $zip=$request->zip;
        $ref=$request->referred_by; 
        if($service_id==null){
            $customer_detail = DB::table('customers');
            if($zip!=null){
                $customer_detail = $customer_detail->Where(function ($query) use($zip) {
                for ($i = 0; $i < count($zip); $i++){
                         $query->orwhere('zip', 'like',  '%' . substr($zip[$i], 0, 3) .'%');
                    }      
                });
            }
            if($ref!=null){
                $customer_detail = $customer_detail->whereIn('referred_by', $ref);
            }
            if($start_date!=null && $end_date!==null){
                $customer_detail = DB::table('customers')->whereBetween('created_at', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"]);
            }
            $customer_detail = $customer_detail->where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->where('email','!=','')->where('email','!=', NULL)->get();
            return view('power_mailing.create',compact('customer_detail','zipcode','services','templates_data'));

        }
        else{
            $booking_ids = DB::table('booking_details')->whereIn('service_id',$service_id)->groupBy('booking_id')->pluck('booking_id')->toArray();

            $customer_detail = DB::table('customers')->join('service_bookings', 'service_bookings.customer_id', '=', 'customers.id')->whereIn('service_bookings.id',$booking_ids);
            if($zip!=null){
                $customer_detail = $customer_detail->Where(function ($query) use($zip) {
                    for ($i = 0; $i < count($zip); $i++){
                             $query->orwhere('customers.zip', 'like',  '%' . substr($zip[$i], 0, 3) .'%');
                        }      
                    });
            }
            if($ref!=null){
                // echo "here"; exit;
                $customer_detail = $customer_detail->whereIn('customers.referred_by', $ref);
            }
            if($start_date!=null && $end_date!=null){
                $customer_detail = whereBetween('service_bookings.created_at', [$request->start_date." 00:00:00", $request->end_date." 23:59:59"]);
            }
            $customer_detail = $customer_detail->where('customers.company_id', auth()->user()->company_id)->orderBy('customers.created_at', 'desc')->where('customers.email','!=','')->where('customers.email','!=', NULL)->get();
            return view('power_mailing.create',compact('customer_detail','zipcode','services','templates_data'));

        }
        
    }

    public function send(Request $request)
    {
        $this->validate($request, 
        [
            'template_id' => 'required',
        ]);
        
        $template_text =  DB::table('templates')->where('id', $request->template_id)->select('title','description')->get();
        $data['description'] = $template_text[0]->description;
        $title = $template_text[0]->title;
        $subject = $template_text[0]->title;
        $emails = explode(",", $request->emails);
        $customer_ids = explode(",", $request->customerids);
        if($request->schedule){
            foreach($emails as $email){
                $data = array(
                    'email'=> $email,
                    'template_id' => $request->template_id,
                    'schedule_date' => $request->schedule_date,
                    'schedule_time' =>$request->schedule_time,
                    'company_id' => auth()->user()->company_id

                );
                DB::table('scheduled_emails')->insert($data);
                $history = array(
                    'email'=> $email,
                    'type'=> 'schedule',
                    'date' => $request->schedule_date,
                    'time' =>$request->schedule_time,
                    'template' => $request->template_id,
                    'company_id' => auth()->user()->company_id
                );
                DB::table('email_marketing_history')->insert($history);
            }
            return back()->with('success','Emails scheduled successfully!');
            //echo $request->schedule_date.'>>>>>>>'.$request->schedule_time; exit;
        }
        else{
            
            Mail::send(['html' => 'emails.template'], $data, function($message) use ($emails,$subject,$title) {
                $message->to($emails)->subject($subject);
                $message->from(Auth::user()->companySetting->email ?? 'noreply@example.com', $title);
            });
            foreach($emails as $email){
                $history = array(
                    'email'=> $email,
                    'type'=> 'sent',
                    'date' => date('Y-m-d'),
                    'time' =>date('h:i a'),
                    'template' => 0,
                    'company_id' => auth()->user()->company_id

                );
                DB::table('email_marketing_history')->insert($history);
            }
            //$this->UnlinkImage($first_image,$second_image);
            if (Mail::failures()) {
                return back()->with('error','Email Sending Error, Please try again!');
            }
            else{
                return back()->with('success','Email sent successfully!');
            }
            
        }

    }
    public function UnlinkImage($firstimage, $secondimage)
    {
        $first_image = public_path().'/power_mailing/'.$firstimage;
        $second_image = public_path().'/power_mailing/'.$secondimage;
        if (file_exists($first_image)) {
        @unlink($first_image);
        }
        if (file_exists($second_image)) {
            @unlink($second_image);
        }
    }

    public function mailing_history()
    {
        $type = 'all';
        $start_date = '';
        $end_date = '';
        $history=DB::table('email_marketing_history')->select('*')->where('company_id', auth()->user()->company_id)->get();
    
        return view('power_mailing.history',compact('history','type','start_date','end_date'));
    }

    public function mailing_history_filter(Request $request)
    {
        $start_date=$request->start_date;
        $end_date=$request->end_date;
        $type=$request->type;
        //dd($request->all());
        $history_detail =DB::table('email_marketing_history')->select('*');
        if($start_date!=null && $end_date!=null){
            $history = $history_detail->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        if($type!='all'){
            $history = $history_detail->where('type', '=',$type);
        }
        $history = $history_detail->where('company_id', auth()->user()->company_id)->get();
        return view('power_mailing.history',compact('history','type','start_date','end_date'));
    }
}
