<?php



namespace App\Http\Controllers\Api;



use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController as BaseController;



use App\Quote;

use Mail;

use DB;

use Validator;



class QuoteController extends BaseController

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $quotations = DB::table('quotes')->where('company_id', $request->company_id)->get();



        if (count($quotations) == 0) {

            return $this->sendResponse($quotations->toArray(), 'Something went wrong.');

        }



        return $this->sendResponse($quotations->toArray(), 'Quotations retrieved successfully.');

    }



    /**

     * Show the form for creating a new resource.

     *bSaloon

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        //

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\RequbSaloonst

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        // dd($request->company_id);

        $to_email = $request->email;

        $quote = $request->message;



        $quotation = new Quote;

        $quotation->email = $request->email;

        $quotation->message = $request->message;

        $quotation->company_id = $request->company_id;

        $quotation->save();



        // below code is working on live host



        $to_email = $request->email;

        $quote = $request->message;



        $data = array("quote" => $quote);

        $title = 'DiaryMaster';

        $subject = 'B Saloon Quotation';



        $data["header"] = '';

        $data["footer"] = '';

        $data["logo"] = '';



        $company_email = DB::table('email_settings')->where('permission', 1)->where('company_id', $request->company_id)->get();

        if(!$company_email->isEmpty()){

            $title = $company_email[0]->title;

            $subject = $company_email[0]->subject;

            

            $data["header"] = $company_email[0]->header;

            $data["footer"] = $company_email[0]->footer;

            $data["logo"] = $company_email[0]->image;

        }

        

        // dd($data["logo"]);

        if($to_email)

        {

            \Mail::send('emails.quote', $data, function($message) use ($to_email,$subject,$title) {

                $message->to($to_email)->subject($subject);

                $message->from('app@diarymaster.co.uk',$title);

            });

            // check for failures

            if (Mail::failures()) {

                return $this->sendError('E-mail not sent.');

            }

            else{

                return $this->sendResponse($quotation, 'E-mail sent successfully!');

            }

        }

    }



    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show(Request $request)

    {

        $quote = DB::table('quotes')->where('company_id', $request->company_id)->where('id', $request->id)->get();



        if(count($quote) == 0){

            return $this->sendResponse($quote->toArray(), 'Something went wrong.');

        }



        return $this->sendResponse($quote->toArray(), 'Quotation retrieved successfully.');

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        //

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        //

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        //

    }

}

