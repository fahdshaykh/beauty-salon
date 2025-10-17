<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Quote;

use Mail;

use DB;



class QuoteController extends Controller

{

    public function index()

    {

        $quotations = DB::table('quotes')->where('company_id', auth()->user()->company_id)->get();

        return view('quotes.index',compact('quotations'));

    }



    public function show($id)

    {

        $quote = DB::table('quotes')->where('company_id', auth()->user()->company_id)->where('id', $id)->get();

        return response()->json($quote);

    }



    public function create()

    {

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

}

