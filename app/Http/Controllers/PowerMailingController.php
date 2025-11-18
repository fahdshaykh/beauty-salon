<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Templates;
use Str;
use Mail;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
  
    // PowerMailingController.php

    // PowerMailingController.php

    public function create(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('email marketing'))) {
            abort(403);
        }
        
        $services = DB::table('services')
            ->select('id', 'name')
            ->where('is_active', 1)
            ->where('company_id', auth()->user()->company_id)
            ->groupBy('name')
            ->get();

        $referreds = DB::table('referred_customers')
            ->select('id', 'referred_by')
            ->where('is_active', 1)
            ->where('company_id', auth()->user()->company_id)
            ->get();    

        $templates_data = Templates::where('type', 3)
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $zipcode = DB::table('customers')
            ->select('zip')
            ->where('company_id', auth()->user()->company_id)
            ->groupBy('zip')
            ->get();

        // Default: show all customers who can receive email
        $customer_detail = collect();

        // If filters are applied (normal submit or AJAX)
        if ($request->filled('start_date') || $request->filled('service_id') || $request->filled('zip') || $request->filled('referred_by') || $request->ajax()) {
            
            $query = DB::table('customers')
                ->where('company_id', auth()->user()->company_id)
                ->where('optout', '!=', 1)
                ->whereNotNull('email')
                ->where('email', '!=', '');

            // Filter by service
            if ($request->filled('service_id')) {
                $booking_ids = DB::table('booking_details')
                    ->whereIn('service_id', $request->service_id)
                    ->groupBy('booking_id')
                    ->pluck('booking_id');

                $query = DB::table('customers')
                    ->join('service_bookings', 'service_bookings.customer_id', '=', 'customers.id')
                    ->whereIn('service_bookings.id', $booking_ids)
                    ->where('customers.company_id', auth()->user()->company_id)
                    ->where('customers.optout', '!=', 1)
                    ->whereNotNull('customers.email')
                    ->where('customers.email', '!=', '')
                    ->select('customers.*');
            }

            // Zip code partial match
            if ($request->filled('zip')) {
                $query->where(function ($q) use ($request) {
                    foreach ($request->zip as $zip) {
                        $q->orWhere('zip', 'like', substr($zip, 0, 3) . '%');
                    }
                });
            }

            // Referred by
            if ($request->filled('referred_by')) {
                $query->whereIn('customers.referred_by', $request->referred_by);
            }

            // Date range
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $from = $request->start_date . ' 00:00:00';
                $to   = $request->end_date . ' 23:59:59';

                if ($request->filled('service_id')) {
                    $query->whereBetween('service_bookings.due_date', [$from, $to]);
                } else {
                    $query->whereBetween('customers.created_at', [$from, $to]);
                }
            }

            $customer_detail = $query->orderBy('created_at', 'desc')->get();
        } else {
            // First page load: show all valid customers
            $customer_detail = DB::table('customers')
                ->where('company_id', auth()->user()->company_id)
                ->where('optout', '!=', 1)
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // If AJAX → return only table
        if ($request->ajax()) {
            return view('power_mailing.partials.customer_table', compact('customer_detail'))->render();
        }

        // Normal load → full page
        return view('power_mailing.create', compact('customer_detail', 'zipcode', 'services', 'referreds', 'templates_data'));
    }

    public function get_template_preview($template_id)
    {
        $template = Templates::select('description')->where('id',$template_id)->first();
        echo $template->description; exit;
    }

    public function send(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('email marketing'))) {
            abort(403);
        }
        
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
            // dd('not schedule');
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
                return redirect()->route('power_mailing.create')->with('error','Email Sending Error, Please try again!');
            }
            else{
                return redirect()->route('power_mailing.create')->with('success','Email sent successfully!');
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

    public function mailing_history(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('email history'))) {
            abort(403);
        }

        $type = $request->input('type', 'all');
        $start_date = $request->input('start_date', '');
        $end_date = $request->input('end_date', '');

        // Base query
        $query = DB::table('email_marketing_history')
            ->where('company_id', auth()->user()->company_id);

        // Apply date filter
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween('date', [$start_date, $end_date]);
        }

        // Apply type filter
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $history = $query->get();

        return view('power_mailing.history', compact('history', 'type', 'start_date', 'end_date'));
    }

    
}
