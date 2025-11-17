<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Quote;
use DateTime;
use Mail;
use DateInterval;
use DatePeriod;
use DB;
use Illuminate\Support\Facades\Auth;

class QuoteController extends Controller

{

    public function index()
    {
        if (! (auth()->user() && auth()->user()->can('view quotations'))) {
            abort(403);
        }

        $quotations = DB::table('quotes')->where('company_id', auth()->user()->company_id)->get();
        return view('quotes.index',compact('quotations'));

    }



    public function show($id)
    {
        if (! (auth()->user() && auth()->user()->can('view quotations'))) {
            abort(403);
        }

        $quote = DB::table('quotes')->where('company_id', auth()->user()->company_id)->where('id', $id)->get();
        return response()->json($quote);

    }



    public function create()
    {
        if (! (auth()->user() && auth()->user()->can('send quotations'))) {
            abort(403);
        }

        return view('quotes.create');

    }



    public function quote_mail(Request $request)

    {

        $this->validate($request, 

            [

                'email' => 'required|email',

            ],

            [

                'email.required' => 'Please enter E-mail',

            ],

        );



        // dd($request->all());

        $to_email = $request->input('email');

        $quote = $request->input('message');



        $quote = new Quote;

        $quote->email = $request->input('email');

        $quote->message = $request->input('message');

        $quote->company_id = auth()->user()->company_id;

        $quote->save();



        // below code is working on live host



        $to_email = $request->input('email');

        $quote = $request->input('message');



        $data = array("quote" => $quote);

        $title = 'B Saloon';

        $subject = 'B Saloon Quotation';



        $data["header"] = '';

        $data["footer"] = '';

        $data["logo"] = '';



        $company_email = DB::table('email_settings')->where('permission', 1)->where('company_id', auth()->user()->company_id)->get();

        if(!$company_email->isEmpty()){

            $title = $company_email[0]->title;

            $subject = $company_email[0]->title.' Quotation';

            

            $data["header"] = $company_email[0]->quotation_header;

            $data["footer"] = $company_email[0]->quotation_footer;

            $data["logo"] = $company_email[0]->image;

        }

        

        // dd($data["logo"]);

        if($to_email)

        {

            \Mail::send('emails.quote', $data, function($message) use ($to_email,$subject,$title) {

                $message->to($to_email)->subject($subject);

                $message->from(Auth::user()->companySetting->email ?? 'noreply@example.com', $title);

            });

            // check for failures

            if (Mail::failures()) {

                return back()->with('error','Email Sending Error, Please try again!');

            }

            else{

            return back()->with('success','Quote has been sent successfully!');

            }

        }

    }



    public function add_holidays()
    {
        if (!(auth()->user() && auth()->user()->can('view holidays'))) {
            abort(403);
        }

                // get rows per page if passed with request
        if (request('rows')) {

            $rows = 500 < intval(request('rows')) ? 500 : intval(request('rows'));
            $rows = 5 > $rows ? 5 : $rows;
            Auth::user()->setSettings(['referred_customers_rows_per_page' => $rows]);
        }
        // otherwise retrieve from user's settings
        $rows = Auth::user()->getSetting('referred_customers_rows_per_page', auth()->user()->default_rows);
        
        $holidays = DB::table('calendar_holidays')->where('company_id',auth()->user()->company_id)->paginate($rows);
        return view('holidays.index',compact('holidays', 'rows'));
    }

    public function update_holidays(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('view holidays'))) {
            abort(403);
        }
        //echo $request->start_date.'>>>>>>'.$request->end_date; exit;
        if($request->start_date!="" && $request->end_date!=""){
            if($request->start_date>$request->end_date){
                return redirect('add-holidays')->with('destroy', 'End Date should not be greater than start date.');
            }
            elseif($request->start_date==$request->end_date){
               $data = array(
                    'company_id' => auth()->user()->company_id,
                   'start_date' => $request->start_date,
                   'end_date' => $request->end_date,
                   'days' => $request->start_date
                   
               );
            }
           
            else{
                $begin = new DateTime($request->start_date);
                $end = new DateTime($request->end_date);
                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);
                $holidays = array();
                foreach ($period as $dt) {
                    $holidays[] = $dt->format("Y-m-d");
                }
                $data = array(
                    'company_id' => auth()->user()->company_id,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'days' => json_encode($holidays)
                    
                );
            }
            // $count = DB::table('calendar_holidays')->where('company_id',auth()->user()->company_id)->get();
            // if($count->count()>0){
            //     DB::table('calendar_holidays')->where('company_id',auth()->user()->company_id)->update($data);
            // }
            // else{
                DB::table('calendar_holidays')->insert($data);
           // }
            
            return redirect('add-holidays')->with('success', 'Holidays Added Successfully.');
           
        }
    }

    public function delete_holidays($id)
    {
        if (! (auth()->user() && auth()->user()->can('delete holidays'))) {
            abort(403);
        }

        DB::table('calendar_holidays')->where('id',$id)->delete();
        return redirect('add-holidays')->with('destroy', 'Holidays Deleted Successfully.');
    }

}

