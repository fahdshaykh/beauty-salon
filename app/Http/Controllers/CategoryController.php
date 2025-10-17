<?php



namespace App\Http\Controllers;

use App\Category;

use App\Services;

use Illuminate\Http\Request;

use DB;
use App\Models\Setting;
use App\Models\UserSetting;


class CategoryController extends Controller

{

    /**

     * Display a listing of the resource.

     *

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
        $categories = Category::select('*')->where('company_id', auth()->user()->company_id)
                                ->orderBy('created_at', 'desc')
                                ->paginate($perPage)
                                ->appends(['per_page' => $perPage]);

        return view('categories.index',compact('categories', 'paginationOptions'));

    }







    /**



     * Show the form for creating a new resource.



     *



     * @return \Illuminate\Http\Response



     */



    public function create()



    {



        return view('categories.create');



    }







    /**



     * Store a newly created resource in storage.



     *



     * @param  \Illuminate\Http\Request  $request



     * @return \Illuminate\Http\Response



     */



    public function store(Request $request)



    {



        $this->validate($request, 



            [

                'name' => 'required',

            ],

            [

                'name.required' => 'Please enter category name',

            ],



        );



        $existing = Category::where('company_id',auth()->user()->company_id)

        ->where('name',$request->input('name'))->count();

        if($existing) {

            session()->flash('error', 'Category Name has already exist');

            return redirect()->back();

        } else {

            $data = $request->all();

            $data['company_id'] = auth()->user()->company_id;    

            Category::create($data);

            return redirect('categories')->with('success', 'Category created successfully');

        }

    }







    /**



     * Display the specified resource.



     *



     * @param  \App\Category  $category



     * @return \Illuminate\Http\Response



     */



    public function show(Category $category)



    {



        //



    }







    public function categoryStatus(Request $request)



    {



        $category = Category::find($request->id);



        $category->is_active = $request->status;



        $category->save();



        return response()->json(['success'=>'Status changed successfully.']);



       



    }







    /**



     * Show the form for editing the specified resource.



     *



     * @param  \App\Category  $category



     * @return \Illuminate\Http\Response



     */



    public function edit(Category $category)



    {



        return view('categories.edit',compact('category'));



    }







    /**



     * Update the specified resource in storage.



     *



     * @param  \Illuminate\Http\Request  $request



     * @param  \App\Category  $category



     * @return \Illuminate\Http\Response



     */



    public function update(Request $request, Category $category)



    {



        $this->validate($request, 



            [



                'name' => 'required|unique:categories,name,'.$category->id,

              



            ],



            [



                'name.required' => 'Please enter category name',

                'name.unique' => 'This category already exists',



            ],



        );







        $data = $request->except('_token','_method');

        DB::table('categories')->where('id', $category->id)->update($data);

        return redirect('categories')->with('success', 'Category updated successfully');



    }







    /**



     * Remove the specified resource from storage.



     *



     * @param  \App\Category  $category



     * @return \Illuminate\Http\Response



     */

     public function destroy(Request $request, Category $category)
     {
        // Check if the category has any associated services
        if ($category->services()->exists()) {
            return redirect()->back()->with('destroy', 'Cannot delete category. It is linked to one or more services.');
        }

        // If no services are linked, proceed with deletion
        $category->delete();
        
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }

}



