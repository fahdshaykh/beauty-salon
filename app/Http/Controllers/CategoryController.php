<?php



namespace App\Http\Controllers;

use App\Category;

use App\Services;

use Illuminate\Http\Request;

use DB;
use App\Models\Setting;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        // Rows per page logic
        if ($request->filled('rows')) {
            $rows = max(5, min(500, (int)$request->rows));
            Auth::user()->setSettings(['categories_rows_per_page' => $rows]);
        }
        $rows = Auth::user()->getSetting('categories_rows_per_page', auth()->user()->default_rows ?? 15);

        // Build query
        $query = Category::where('company_id', auth()->user()->company_id);

        // Search by category name
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Filter by creation date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Execute with latest first + preserve all filters in pagination links
        $categories = $query->latest()->paginate($rows)->withQueryString();

        return view('categories.index', compact('categories', 'rows'));
    }

    /**



     * Show the form for creating a new resource.



     *



     * @return \Illuminate\Http\Response



     */



    public function create()



    {


        $this->authorize('create', Category::class);

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


        $this->authorize('create', Category::class);

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


        $this->authorize('update', $category);

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

        $this->authorize('update', $category);


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

        $this->authorize('delete', $category);

        // Check if the category has any associated services
        if ($category->services()->exists()) {
            return redirect()->back()->with('destroy', 'Cannot delete category. It is linked to one or more services.');
        }

        // If no services are linked, proceed with deletion
        $category->delete();
        
        return redirect()->route('categories.index')->with('destroy', 'Category deleted successfully.');
    }

}



