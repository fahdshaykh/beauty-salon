<?php







namespace App\Http\Controllers;







use Illuminate\Http\Request;



use Session;

use DateTime;
use DatePeriod;
use DateInterval;

use DB;



use Str;

use File;









class SettingController extends Controller



{



    public function theme_settings()



    {   



        $settings = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->first();

   

     

        $email = DB::table('email_settings')->where('company_id', auth()->user()->company_id)->get();

     



        return view('settings.theme_settings',compact('email','settings'));



    }







    public function theme_settings_edit($id)



    {



        $theme = DB::table('theme_settings')->where('company_id', auth()->user()->company_id)->get();



        return view('settings.theme_settings_edit',compact('theme'));



    }







    public function theme_settings_update(Request $request)



    {

        $id=auth()->user()->company_id;

       

        $this->validate($request, 

            [

                'site_name' => 'required',
                'financial_year'=>'required'

            ],

            [

                'site_name.required' => 'Please enter site name',
                'financial_year.required' => 'Please enter financial year',

            ],

        );

        if($request->site_logo){



            $validate = $request->validate([



                'site_logo' => 'mimes:jpeg,jpg,png,gif|required|max:2048',



            ]);

            //checking if logo already exist then delete previous logo

            $result =  DB::table('company_settings')->select('site_logo')->where('company_id', $id)->first(); 

            $image_path = public_path()."/company_thumbnail/".$result->site_logo;  // Value is not URL but directory file path

            if(File::exists($image_path)) {

                File::delete($image_path);

            }

            $image = $request->file('site_logo');

            $image_name = '';

            $destinationPath = public_path().'/company_thumbnail/';

            $image->move($destinationPath, $image_name = 'img_'.Str::random(15).'.'. $image->getClientOriginalExtension());

            shell_exec("convert $destinationPath -resize 250x100\! $image_name");

            $data['site_logo'] = $image_name;

            Session::put('site_logo', $image_name);



        }



        if($request->smart_logo){

           



            $validate = $request->validate([



                'smart_logo' => 'mimes:jpeg,jpg,png,gif|required|max:2048',



            ]);

            //checking if logo already exist then delete previous logo

            $result =  DB::table('company_settings')->select('smart_logo')->where('company_id', $id)->first(); 

           

            $image_path = public_path()."/company_thumbnail/".$result->smart_logo;  // Value is not URL but directory file path

            if(File::exists($image_path)) {

                File::delete($image_path);

            }

           

            $image = $request->file('smart_logo');

            $image_name = '';

            $destinationPath = public_path().'/company_thumbnail/';

            $image->move($destinationPath, $image_name = 'img_'.Str::random(15).'.'. $image->getClientOriginalExtension());

            shell_exec("convert $destinationPath -resize 250x100\! $image_name");

            $data['smart_logo'] = $image_name;

            Session::put('smart_logo', $image_name);



        }





        $data['site_name'] = $request->site_name;

        $data['smart_name'] = $request->smart_name;
        $data['financial_year'] = $request->financial_year;
        // $data['site_logo'] = time().$originalImage->getClientOriginalName();



        $data['company_id'] = auth()->user()->company_id;

       

        DB::table('company_settings')->where('company_id', $id)->update($data);



     

        return redirect('theme-settings')->with(['success'=>'Theme Setting updated successfully.']);



    }



    public function message_status(Request $request)



    {



        $data = $request->all();



        $data = $request->except('_token','_method');



        $id = $request->id;



        $data['message_permissions'] = $request->message_permissions;



        DB::table('company_settings')->where('company_id',$id)->update($data);



        return response()->json(['success'=>'Status changed successfully.']);



    }

    public function tax_status(Request $request)



    {



        $data = $request->all();

    

        $data = $request->except('_token','_method');



        $company_id = auth()->user()->company_id;



        $data['tax_status'] = $request->tax_status;



        DB::table('company_settings')->where('company_id',$company_id)->update($data);



        return response()->json(['success'=>'Status changed successfully.']);



    }



    public function email_settings_update(Request $request)



    {

      

        $data = $request->except('_token','files');







        if($request->image){



            $validate = $request->validate([



                'image' => 'required|max:2048',



            ]);



            // $originalImage= $request->file('site_logo');



            // $thumbnailImage = Image::make($originalImage);



            // $thumbnailPath = public_path().'/company_thumbnail/';



            // // $originalPath = public_path().'/company/';



            // // $thumbnailImage->save($originalPath.time().$originalImage->getClientOriginalName());



            // $thumbnailImage->resize(250,100);



            // $thumbnailImage->save($thumbnailPath.time().$originalImage->getClientOriginalName());











            //new image code without package



            $image = $request->file('image');







            $image_name = rand(111111, 888999)*time() .'.'. $image->getClientOriginalExtension();



            $thumb_name = rand(111111, 888999)*time() .'.'. $image->getClientOriginalExtension();



            $destinationPath = public_path().'/email_logo/';







            $image->move($destinationPath, $image_name);



            $orgImgPath = $destinationPath. '/'.$image_name;



            $thumbPath = $destinationPath. '/'.$thumb_name;



            shell_exec("convert $orgImgPath -resize 250x100\! $thumbPath");



            $data['image'] = $image_name;



        }



        $id=auth()->user()->company_id;

        $data['company_id'] = auth()->user()->company_id;



        DB::table('email_settings')->where('company_id', $id)->update($data);



        return response()->json(['success'=>'Status changed successfully.']);



    }



    public function review_settings_edit(Request $request)



    {

        

        $review_settings = DB::table('review_settings')->where('company_id', auth()->user()->company_id)->get();



        return view('settings.review_edit',compact('review_settings'));



    }



    public function review_settings_update(Request $request)



    {

        $review = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update(['review_url' => $request->url]);



       

        return response()->json(['success'=>'URL updated successfully.']);

        



    }



    public function clicksend_update(Request $request)



    {

        $review = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update(['clicksend_username' => $request->clicksend_username, 'clicksend_password'=>$request->clicksend_password, 'clicksend_api_key'=>$request->clicksend_apikey, 'clicksend_returnaddress'=>$request->clicksend_returnaddress]);

       

        return response()->json(['success'=>'Clicksend credentials updated successfully.']);

        



    }



    public function socialmeida_update(Request $request)



    {

        $review = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update(['facebook' => $request->facebook, 'twitter'=>$request->twitter, 'instagram'=>$request->instagram]);

       

        return response()->json(['success'=>'Social Medai Links Updated Successfully.']);

        



    }

    public function tax_settings_update(Request $request)



    {

      

       $vat=$request->vat;

       $amount=$request->amount;

        $review = DB::table('company_settings')->where('company_id', auth()->user()->company_id)->update(['amount' => $amount ,'vat'=>$vat]);





        return response()->json(['success'=>'Tax Settings Updated successfully']);



    }

    public function review_status(Request $request)



    {



        $data = $request->all();



        $data = $request->except('_token','_method');



        $id =  auth()->user()->company_id;

 



        $data['review_permissions'] = $request->review_permissions;



        DB::table('company_settings')->where('company_id',$id)->update(['review_permissions'=>$request->review_permissions]);

        



        return response()->json(['success'=>'Status changed successfully.']);



    }



    public function booking_message_update(Request $request)



    {

  



        $data = $request->all();

      

       

        $data = $request->except('_token','_method');



        $id = auth()->user()->company_id;



        $data['booking_sms'] = $request->booking_sms;

        $data['re_sheduled_sms'] = $request->re_sheduled_sms;

        $data['survey_sms'] = $request->survey_sms;



        DB::table('company_settings')->where('company_id',$id)->update($data);



        return response()->json(['success' =>'Booking message updated successfully.']);

        



    }

    public function invoice_settings(Request $request){
  
        if($request->start_date!="" && $request->end_date!=""){
            // $affected = DB::table('service_bookings')
            // ->whereDate('due_date', '>=' , $request->start_date)
            // ->whereDate('due_date', '<=' , $request->end_date)
            //   ->update(['total_price' => 0]);
            
        
            return redirect('invoice-setting')->with('success', $affected.' Invoice Updated Successfully.');
        }
        return view('settings.invoice_setting');
    }

    public function add_holidays(){
        
        $holidays = DB::table('calendar_holidays')->where('company_id',auth()->user()->company_id)->get();
        return view('holidays.index',compact('holidays'));
    }

    public function update_holidays(Request $request){
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

    public function delete_holidays($id){
        DB::table('calendar_holidays')->where('id',$id)->delete();
        return redirect('add-holidays')->with('success', 'Deleted Successfully.');
    }




}



