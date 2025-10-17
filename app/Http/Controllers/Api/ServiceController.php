<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Category;
use App\Services;
use DB;
use Validator;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $services = Services::select('*')->where('company_id', $request->company_id)->orderBy('created_at', 'desc')->get();

        if ($services->isEmpty()) {
            return $this->sendResponse($services->toArray(), 'Something went wrong');
        }

        return $this->sendResponse($services->toArray(), 'Services retrieved successfully.');
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


    public function cateandservices(Request $request){
        $services = Category::whereHas('services_booking')->where('company_id',  $request->company_id)->where('is_active', 1)->get();
        foreach($services as $category){
            foreach($category['services_booking'] as $service){
                if($service->is_active==1){
                    $all_services[$category->id][$service->id]= $service->name; 
                }
                
            }
        }
        //print_r($all_services); exit;
        if ($services->isEmpty()) {
            $all_services = '';
            return $this->sendResponse($all_services, 'Something went wrong');
        }

        return $this->sendResponse($all_services, 'Services retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'company_id' => 'required',
            'category_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $existing = Services::where('company_id',$request->input('company_id'))
        ->where('name',$request->input('name'))->where('category_id',$request->input('category_id'))
        ->count();
        if($existing) {
            return $this->sendError('Service already exist!');
        } else {
            // $exists = DB::table('services')
            // ->where('name',$request->input('name'))
            // ->get();
            // if(!$exists->isEmpty()) {
            //     $data = $request->all();
            //     $data = $request->except('_token','_method');
            //     $service = tap(DB::table('services')->where('id', $exists['0']->id))
            //     ->update($data)
            //     ->first();

            //     return $this->sendResponse($service, 'Service Created successfully.');
            // }
            //$data = $request->all();
            $service_data  = array(
                'name'=> $request->input('name'),
                'category_id'=> $request->input('category_id'),
                'price'=> $request->input('price'),
                'service_color'=> $request->input('service_color'),
                'company_id' => $request->input('company_id')
            );
            $service = Services::create($service_data);
            return $this->sendResponse($service->toArray(), 'Service Created successfully.');
        }
       
    }

    public function servicestatus(Request $request)
    {

        $service = tap(DB::table('services')
        ->where('id', $request->id)->where('company_id', $request->company_id))
        ->update(['is_active' => $request->status])
        ->first();

        if ($service) {
            return $this->sendResponse($service, 'Status changed successfully.');
        } else {
            return $this->sendResponse((array)$service, 'Something went wrong.');
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
        $input = $request->all();

        $validator = Validator::make($input, [
            'id' => 'required',
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $service = DB::table('services')
                                ->where('company_id', $request->company_id)
                                ->where('id',$request->id)
                                ->get();
        if(count($service) == 0){
            return $this->sendResponse($service, 'Something went wrong.');
        }
        return $this->sendResponse($service, 'Service retrieved successfully.');
    }

    public function activeServices(Request $request)
    {
        $services = Services::select('*')->where('is_active', 1)->where('company_id', $request->company_id)->get();

        if(count($services) == 0){
            return $this->sendResponse($services, 'Something went wrong.');
        }

        return $this->sendResponse($services, 'Service retrieved successfully.');
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
    public function update(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'id' => 'required',
            'company_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $data = $request->all();

        $service = tap(DB::table('services')->where('id', $request->id)->where('company_id',$request->company_id))
        ->update($data)
        ->first();
        if ($service == null) {
            return $this->sendError('Something went wrong.');
        }
        return $this->sendResponse($service, 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function destroy(Request $request)
    // {
    //     $input = $request->all();

    //     $validator = Validator::make($input, [
    //         'id' => 'required',
    //         'company_id' => 'required'
    //     ]);

    //     if($validator->fails()){
    //         return $this->sendError('Validation Error.', $validator->errors());       
    //     }

    //     $service = tap(DB::table('services')
    //     ->where('id', $request->id)->where('company_id', $request->company_id))
    //     ->update(['is_deleted' => 1])
    //     ->first();
        
    //     if ($service == null) {
    //         return $this->sendError('Something went wrong.');
    //     }

    //     return $this->sendResponse($service, 'Service deleted successfully.');
        
    // }
}
