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
use Illuminate\Support\Facades\Auth;

class ServicesController extends Controller

{

    /**

     * Display a listing of the resource.

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        $this->authorize('viewAny', Services::class);

        // Rows per page logic
        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int)$request->rows));
            Auth::user()->setSettings(['services_rows_per_page' => $rows]);
        }
        $rows = Auth::user()->getSetting('services_rows_per_page', auth()->user()->default_rows ?? 15);

        // Build query
        $query = Services::where('company_id', auth()->user()->company_id);

        // Search by category id
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by creation date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Execute: latest first + preserve all filters in pagination links
        $services = $query->latest()->paginate($rows)->withQueryString();
        
        $categories = Category::select('*')->where('company_id', auth()->user()->company_id)->where('is_active', 1)->get();

        return view('services.index', compact('services', 'rows', 'categories'));
    }







    /**



     * Show the form for creating a new resource.



     *



     * @return \Illuminate\Http\Response



     */



     public function create()



    {

        $this->authorize('create', Services::class);

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
        $this->authorize('create', Services::class);
        
         $validate = $request->validate([
             'category_id' => 'required',
             'name' => 'required|max:255|unique:services,name,NULL,id,company_id,' . auth()->user()->company_id . ',category_id,' . $request->input('category_id'),
             'price_type' => 'required|max:255',
             'price' => 'required|numeric',
         ]);
     
         $service = new Services;
         $service->category_id = $request->input('category_id');
         $service->name = $request->input('name');
         $service->price_type = $request->input('price_type');
         $service->risk_status = $request->input('risk_status');
         $service->price = $request->input('price');
         $service->service_color = $request->input('service_color');
         $service->service_time = $request->input('service_time');
         $service->description = $request->input('description');
         $service->company_id = auth()->user()->company_id;
         $service->save();

         if ($request->has('employees')) {
            $service->employees()->attach($request->employees);
        }
     
         return back()->with('success', 'Service Created Successfully');
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

        $user = auth()->user();
        
        // For non-admins, only load expenses from their company
        $query = Services::where('id', $id);
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $query->where('company_id', $user->company_id);
        }
        
        $services = $query->firstOrFail();
        
        // Still check the policy for permission-based access
        if (!($user && method_exists($user, 'hasRole') && $user->hasRole('admin'))) {
            $this->authorize('update', $services);
        }


        $employees = User::select('*')->where('is_active', 1)->where('company_id', auth()->user()->company_id)->where('is_deleted', 0)->orderBy('created_at', 'desc')->get();


        $categories = Category::select('*')->where('company_id', auth()->user()->company_id)->get();



        // $services = Services::find($id);



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

         $this->authorize('update', $service);

         $service->category_id = $request->input('category_id');
         $service->name = trim($request->input('name'));
         $service->price_type = $request->input('price_type');
         $service->risk_status = $request->input('risk_status');
         $service->price = $request->input('price');
         $service->service_color = $request->input('service_color');
         $service->service_time = $request->input('service_time');
         $service->description = $request->input('description');
         $service->company_id = auth()->user()->company_id;
         $service->save();

         $service->employees()->sync($request->employees);
     
         return back()->with('success', 'Service has been Updated Successfully');
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

    //     return back();



    // }

    public function destroy(Request $request, Services $service)
    {
        $this->authorize('delete', $service);

        // Check if the service is linked to any bookings in the BookingDetail pivot table
        if (DB::table('booking_details')->where('service_id', $service->id)->exists()) {
            return redirect()->back()->with('error', 'Cannot delete service. It is linked to one or more bookings.');
        }

        // Proceed with deletion if no bookings are linked
        try {
            $service->delete();
            return redirect()->route('services.index')->with('destroy', 'Service deleted successfully.');
        } catch (\Exception $e) {
            // \Log::error("Error deleting service: {$e->getMessage()}");
            return redirect()->back()->with('error', 'An error occurred while deleting the service.');
        }
    }



}



