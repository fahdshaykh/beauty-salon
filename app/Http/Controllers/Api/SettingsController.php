<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use DB;
use DateTime;
use DatePeriod;
use DateInterval;
use Validator;

class SettingsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sms_status(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $message_status = DB::table('company_settings')->select('*')->where('company_id', $request->company_id)->pluck('message_permissions');
        
        if ($message_status->isEmpty()) {
            return $this->sendError('', 'Something went wrong.');
        }
        $data['message_permission'] = $message_status[0];
        return $this->sendResponse($data, 'Status retrieved successfully.');
    }


    public function get_holidays(Request $request){
        $input = $request->all();

        $validator = Validator::make($input, [
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $holidays = DB::table('calendar_holidays')->select('*')->where('company_id', $request->company_id)->get();
        
        if ($holidays->isEmpty()) {
            return $this->sendError('', 'There is no data.');
        }
        foreach($holidays as $key=>$holiday){
            $data[$key] = array(
            'id' => $holiday->id,
            'statr_date' => $holiday->start_date,
            'end_date' => $holiday->end_date,
            'days' => json_decode($holiday->days)
        );
        }
       
        return $this->sendResponse($data, 'Holidays retrieved successfully.');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   
    public function update_holidays(Request $request){
        $input = $request->all();

        $validator = Validator::make($input, [
            'start_date' => 'required',
            'end_date' => 'required',
            'company_id' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        if($request->start_date==$request->end_date){
            $data = array(
                 'company_id' => $request->company_id,
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
                'company_id' => $request->company_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days' => json_encode($holidays)
                
            );
        }

            DB::table('calendar_holidays')->insert($data);

        return $this->sendResponse($data, 'Holidays Update successfully.');
    }
  

    public function update_sms_status(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'company_id' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $result = DB::table('company_settings')->where('company_id', $request->company_id)->update(array('message_permissions'=>$request->status));
        if($result){
            $data['message_permission'] = $request->status;
        }
        else{
            return $this->sendError('', $validator->errors()); 
        }
        return $this->sendResponse($data, 'Status updated Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete_holidays(Request $request){
        $input = $request->all();

        $validator = Validator::make($input, [
            'company_id' => 'required',
            'id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $result = DB::table('calendar_holidays')->where('id',$request->id)->delete();
        return $this->sendResponse($result, 'Holidays Deleted Successfully.');
    }
   
}
