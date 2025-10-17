<?php



namespace App\Http\Controllers\Api;



use Illuminate\Http\Request;

use App\Http\Controllers\Api\BaseController as BaseController;



use App\Category;

use App\Services;

use DB;

use Validator;



class CategoryController extends BaseController

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {

        $categories = Category::select('*')->where('company_id', $request->company_id)->orderBy('created_at', 'desc')->get();

        

        if ($categories->isEmpty()) {

            return $this->sendError($categories->toArray(), 'There is no data.');

        }



        return $this->sendResponse($categories->toArray(), 'categories retrieved successfully.');

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

    public function store(Request $request)

    {

        $input = $request->all();



        $validator = Validator::make($input, [

            'name' => 'required',

            'company_id' => 'required'

        ]);



        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }



        $existing = Category::where('company_id', $request->company_id)

        ->where('name',$request->input('name'))

        ->count();

        if($existing) {

            return $this->sendError($empty = [],'Category already exist!');

        } else {

            $exists = DB::table('categories')->where('name',$request->input('name'))

            ->get();

            if(!$exists->isEmpty()) {

                $data = $request->all();

                $data = $request->except('_token','_method');



                $category = tap(DB::table('categories')->where('id', $exists['0']->id))

                ->update($data)

                ->first();



                // dd('category deleted updated');



                return $this->sendResponse($category, 'Category created successfully.');

            }

        }

        $data = $request->all();

        $category = Category::create($data);

        return $this->sendResponse($category->toArray(), 'Category created successfully.');

    }



    public function categoryStatus(Request $request)

    {

        $category = tap(DB::table('categories')

        ->where('id', $request->id)->where('company_id', $request->company_id))

        ->update(['is_active' => $request->status])

        ->first();



        if ($category) {

            return $this->sendResponse($category, 'Status changed successfully.');

        } else {

            return $this->sendError((array)$category, 'Something went wrong.');

        }



    }



    public function activeCategories(Request $request)

    {

        $categories = Category::select('*')->where('is_active', 1)->where('company_id', $request->company_id)->get();



        if(count($categories) == 0){

            return $this->sendError('Something went wrong.');

        }



        return $this->sendResponse($categories, 'Service retrieved successfully.');

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



        $category = DB::table('categories')

                                ->where('company_id', $request->company_id)

                                ->where('id',$request->id)

                                ->get();

    

        if (count($category) == 0) {

            return $this->sendResponse($category->toArray(), 'Something went wrong.');

        }



        return $this->sendResponse($category->toArray(), 'Category retrieved successfully.');

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

            'company_id' => 'required'

        ]);



        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }



        $data = $request->all();



        $category = tap(DB::table('categories')->where('id', $request->id)->where('company_id', $request->company_id))

        ->update($data)

        ->first();

        if ($category == null) {

            return $this->sendError('Something went wrong.');

        }

        return $this->sendResponse($category, 'Category Updated successfully.');

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy(Request $request)

    {

        $input = $request->all();



        $validator = Validator::make($input, [

            'id' => 'required',

            'company_id' => 'required'

        ]);



        if($validator->fails()){

            return $this->sendError('Validation Error.', $validator->errors());       

        }

        

        $category = Services::where('category_id',$request->id)->where('company_id', $request->company_id)->get();



        $cat = $category->count();



        if($cat>0){

            return $this->sendError('Operation Denied. Category Is Related To Services Table.');

        }

        else{

            

            if($cat == 0){

                $category = tap(DB::table('categories')

                ->where('id', $request->id)->where('company_id', $request->company_id))

                ->first();



                return $this->sendResponse($category, 'Category Deleted successfully.');

            }



            return $this->sendError('Something went wrong.');

            

        }

    }

}

