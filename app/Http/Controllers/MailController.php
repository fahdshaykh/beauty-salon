<?php







namespace App\Http\Controllers;







use Illuminate\Http\Request;



//use Illuminate\Support\Facades\Mail;



use Mail;
use App\ServiceBooking;
use App\Mail\InvoiceMailable;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDF;





class MailController extends Controller



{

  



    public function invoice_mail($id)
    {
        try {
            $booking_detail = ServiceBooking::with('customer')->where('id', $id)->get();

            $detail_array = "";
            $payment_instrument = 'Not Available';
            $chequenumber= '';

            foreach($booking_detail as $book){

                if($book->payment_instrument!=""){
                    $payment_instrument = $book->payment_instrument;
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
                    'payment_instrument' => $payment_instrument,
                    'payment_days' => $book->payment_days,
                    'vat' => $book->vat,
                    'chequenumber' => $chequenumber,
                    'postcode' => $book->customer->zip,
                    'services' => $this->get_services($book->id),
                );
            }

            $title = 'ABC Company';
            $subject = 'Booking Invoice';

            $detail_array["header"] = '';
            $detail_array["footer"] = '';
            $detail_array["company_logo"] = '';

            $company_email = DB::table('email_settings')->where('company_id', auth()->user()->company_id)->get();
            // dd($company_email[0]->image);
            if(!$company_email->isEmpty()){
                $title = $company_email[0]->title;
                $subject = $company_email[0]->subject;
                $detail_array["header"] = $company_email[0]->header;
                $detail_array["footer"] = $company_email[0]->footer;
                $detail_array["company_logo"] = $company_email[0]->image;
            }

            // Generate PDF
            $pdf = \PDF::loadView('emails.invoicepdf', compact('detail_array'));

            $to_name = $detail_array['last_name'];
            $to_email = $detail_array['email'];

            $fromAddress = Auth::user()->companySetting->email ?? 'noreply@example.com';
            $fromName = $title;

            // Queue the invoice email (pass booking ID, not PDF content)
            Mail::to($to_email, $to_name)->queue(new InvoiceMailable(
                $detail_array,
                $to_email,
                $to_name,
                $subject,
                $fromAddress,
                $fromName,
                $id
            ));

            return response()->json(['success' => 'E-mail has been queued and will be sent shortly']);

        } catch (\Exception $e) {
            Log::error('Invoice email error: ' . $e->getMessage());
            return response()->json(['error' => 'There is error in sending mail: ' . $e->getMessage()], 500);
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

        $title = 'ABC Company';

        $subject = 'Booking Invoice';



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



