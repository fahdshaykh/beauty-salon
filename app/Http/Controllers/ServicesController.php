<?php

namespace App\Http\Controllers;

use App\Services;

use Illuminate\Http\Request;
use App\User;
use App\Category;
use App\Models\Setting;
use App\Models\UserSetting;

use Session;

use DB;

class ServicesController extends Controller

{

    /**

     * Display a listing of the resource.

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $paginationOptions = Setting::getPaginationOptions();
        $perPage = $request->input('per_page', auth()->user()->getPerPagePreference());
        if (!in_array($perPage, $paginationOptions)) {
            $perPage = auth()->user()->getPerPagePreference();
        }
        if ($request->has('per_page')) {
            if (auth()->user()->setting) {
                auth()->user()->setting->update(['per_page' => $perPage]);
            } else {
                UserSetting::create(['user_id' => auth()->user()->id, 'per_page' => $perPage]);
            }
        }

        $services = Services::select('*')->where('company_id', auth()->user()->company_id)
                                ->orderBy('created_at', 'desc')
                                ->paginate($perPage)
                                ->appends(['per_page' => $perPage]);

    //    echo "<pre>"; print_r($services); exit;

        return view('services.index',compact('services', 'paginationOptions'));



    }







    /**



     * Show the form for creating a new resource.



     *



     * @return \Illuminate\Http\Response



     */



     public function create()



    {


        $employees = User::select('*')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();

        $categories = Category::select('*')->where('company_id', auth()->user()->company_id)->where('is_active', 1)->get();

        return view('services.create',compact('categories', 'employees'));



    }





    /**



     * Store a newly created resource in storage.



     *



     * @param  \Illuminate\Http\Request  $request



     * @return \Illuminate\Http\Response



     */



     public function store(Request $request)
     {
         $validate = $request->validate([
             'category_id' => 'required',
             'name' => 'required|max:255|unique:services,name,NULL,id,company_id,' . auth()->user()->company_id . ',category_id,' . $request->input('category_id'),
             'price_type' => 'required|max:255',
             'price' => 'numeric|nullable',
         ]);
     
         $service = new Services;
         $service->category_id = $request->input('category_id');
         $service->name = $request->input('name');
         $service->price_type = $request->input('price_type');
         $service->risk_status = $request->input('risk_status');
         $service->price = $request->input('price');
         $service->service_color = $request->input('service_color');
         $service->description = $request->input('description');
         $service->company_id = auth()->user()->company_id;
         $service->save();

         if ($request->has('employees')) {
            $service->employees()->attach($request->employees);
        }
     
         return redirect('services')->with('success', 'Service Created Successfully');
     }
     


    /**



     * Display the specified resource.



     *



     * @param  \App\Services  $services



     * @return \Illuminate\Http\Response



     */



    public function show(services $services)



    {



        //



    }







    public function servicestatus(Request $request)

    {

        $service = Services::find($request->id);

        $service->is_active = $request->status;

        $service->save();

        return response()->json(['success'=>'Status changed successfully.']);

    }







    // public function get_services(Request $request)



    // {



    //     $services = DB::table('services')->where('is_deleted',0)->where('company_id', auth()->user()->company_id)->where('category_id', $request->category_id)->get();



    //     return response()->json($services);



    // }



    /**



     * Show the form for editing the specified resource.



     *



     * @param  \App\Services  $services



     * @return \Illuminate\Http\Response



     */



    public function edit($id)



    {


        $employees = User::select('*')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();


        $categories = Category::select('*')->where('company_id', auth()->user()->company_id)->get();



        $services = Services::find($id);



        return view('services.edit',compact('services','categories', 'employees'));



    }







    /**



     * Update the specified resource in storage.



     *



     * @param  \Illuminate\Http\Request  $request



     * @param  \App\Services  $services



     * @return \Illuminate\Http\Response



     */



     public function update(Request $request, $id)
     {
         $validate = $request->validate([
             'category_id' => 'required',
             'name' => 'required|max:50|unique:services,name,' . $id . ',id,company_id,' . auth()->user()->company_id,
             'price' => 'required|numeric',
         ]);
     
         $service = Services::findOrFail($id);
         $service->category_id = $request->input('category_id');
         $service->name = trim($request->input('name'));
         $service->price_type = $request->input('price_type');
         $service->risk_status = $request->input('risk_status');
         $service->price = $request->input('price');
         $service->service_color = $request->input('service_color');
         $service->description = $request->input('description');
         $service->company_id = auth()->user()->company_id;
         $service->save();

         $service->employees()->sync($request->employees);
     
         return redirect('services')->with('success', 'Service has been Updated Successfully');
     }








    /**



     * Remove the specified resource from storage.



     *



     * @param  \App\Services  $services



     * @return \Illuminate\Http\Response



     */



    // public function destroy($id)

    // {

    //     $service = Services::find($id);

    //     $service->is_deleted = 1;

    //     $service->save();

    //     session()->flash('destroy','Service deleted Successfully');

    //     return redirect('services');



    // }

    public function destroy(Request $request, Services $service)
    {
        // Check if the service is linked to any bookings in the BookingDetail pivot table
        if (DB::table('booking_details')->where('service_id', $service->id)->exists()) {
            return redirect()->back()->with('success', 'Cannot delete service. It is linked to one or more bookings.');
        }

        // Proceed with deletion if no bookings are linked
        try {
            $service->delete();
            return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
        } catch (\Exception $e) {
            // \Log::error("Error deleting service: {$e->getMessage()}");
            return redirect()->back()->with('success', 'An error occurred while deleting the service.');
        }
    }



}



