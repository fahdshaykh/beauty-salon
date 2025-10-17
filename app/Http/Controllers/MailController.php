<?php







namespace App\Http\Controllers;







use Illuminate\Http\Request;



//use Illuminate\Support\Facades\Mail;



use Mail;

use App\ServiceBooking;

use DB;

use PDF;





class MailController extends Controller



{

  



    public function invoice_mail($id)

    {

        $booking_detail = ServiceBooking::with('customer')->where('id', $id)->get();

        $detail_array = "";

        $payment_method = 'Not Available';

        $chequenumber= '';

        foreach($booking_detail as $book){

            if($book->payment_method!=""){

                $payment_method = $book->payment_method;

                $chequenumber= $book->chequenumber;

            }

            $detail_array = array(

                'invoice' =>  $book->invoice,

                'due_date' => $book->due_date,

                'total_price' => $book->total_price,

                'first_name' =>  $book->customer->first_name,

                'last_name' =>  $book->customer->last_name,

                'primary_phone' => $book->customer->primary_phone,

                'email' => $book->customer->email,

                'address' =>$book->customer->address ,

                'comment' => $book->comment,

                'payment_method' => $payment_method,

                'payment_days' => $book->payment_days,

                'vat' => $book->vat,

                'chequenumber' => $chequenumber,

                'postcode' => $book->customer->zip,

                'services' => $this->get_services($book->id),

                );

        }

        $title = 'Diary Mastter';

        $subject = 'B Saloon Invoice';



        $detail_array["header"] = '';

        $detail_array["footer"] = '';

        $detail_array["company_logo"] = '';



        $company_email = DB::table('email_settings')->where('company_id', auth()->user()->company_id)->get();

        if(!$company_email->isEmpty()){

            $title = $company_email[0]->title;

            $subject = $company_email[0]->subject;

            $detail_array["header"] = $company_email[0]->header;

            $detail_array["footer"] = $company_email[0]->footer;

            $detail_array["company_logo"] = $company_email[0]->image;

        }

        // return view('emails.invoicepdf', compact('detail_array'));

        // exit();

        $pdf = \PDF::loadView('emails.invoicepdf', compact('detail_array')); 
        // return $pdf->stream('invoice.pdf');
        // exit();

        $to_name = $detail_array['last_name'];

        $to_email = $detail_array['email'];



        \Mail::send('emails.invoice', compact('detail_array'), function($message) use ($to_name, $to_email,$title,$subject,$pdf) {

            $message->to($to_email, $to_name)->subject($subject);

            $message->from(Auth::user()->companySetting->email ?? 'noreply@example.com', $title);

            $message->attachData($pdf->output(), "invoice.pdf");

        });

        // check for failures

        $failures =  Mail:: failures();

        if(count($failures)>0){

            

            return response()->json(['error' => 'There is error in sending mail.']);

           // return back()->with('error','There is error in sending mail.');

        }

        else{

            return response()->json(['success'=>'E-mail has been sent successfully']);

            //return back()->with('success','E-mail has been sent successfully!');

        }

    }


    public function sms_invoice_mail($id,$company)

    {

        $booking_detail = ServiceBooking::with('customer')->where('id', $id)->get();

        $detail_array = "";

        $payment_method = 'Not Available';

        $chequenumber= '';

        foreach($booking_detail as $book){

            if($book->payment_method!=""){

                $payment_method = $book->payment_method;

                $chequenumber= $book->chequenumber;

            }

            $detail_array = array(

                'invoice' =>  $book->invoice,

                'due_date' => $book->due_date,

                'total_price' => $book->total_price,

                'first_name' =>  $book->customer->first_name,

                'last_name' =>  $book->customer->last_name,

                'primary_phone' => $book->customer->primary_phone,

                'email' => $book->customer->email,

                'address' =>$book->customer->address ,

                'comment' => $book->comment,

                'payment_method' => $payment_method,

                'payment_days' => $book->payment_days,

                'vat' => $book->vat,

                'chequenumber' => $chequenumber,

                'postcode' => $book->customer->zip,

                'services' => $this->get_services($book->id),

                );

        }

        $title = 'Diary Mastter';

        $subject = 'B Saloon Invoice';



        $detail_array["header"] = '';

        $detail_array["footer"] = '';

        $detail_array["company_logo"] = '';



        $company_email = DB::table('email_settings')->where('company_id', $company)->get();

        if(!$company_email->isEmpty()){

            $title = $company_email[0]->title;

            $subject = $company_email[0]->subject;

            $detail_array["header"] = $company_email[0]->header;

            $detail_array["footer"] = $company_email[0]->footer;

            $detail_array["company_logo"] = $company_email[0]->image;

        }

        return view('emails.invoicepdf', compact('detail_array'));

        exit();

        // $pdf = \PDF::loadView('emails.invoicepdf', compact('detail_array')); 
        // return $pdf->download('invoice.pdf');
        // // exit();




       

     


    }




    public function get_services($id){

        $services = array();

        $servicenames = DB::table('booking_details')->join('services','services.id','=','booking_details.service_id')->where('booking_details.booking_id', $id)->get();

        foreach($servicenames as $service){

            $services[]=  array(

                'service_name' => $service->name,

                'service_price' => $service->price,

            );

        }

        return $services;

    }



    







}



