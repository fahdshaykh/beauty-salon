<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BookingNotes;
use DB;
use Str;
class JsGridController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        //
      
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
         
       $notes = new BookingNotes;
       $notes->booking_id = $request->id;
       $notes->details = $request->details;
        if($request->hasFile('img')){

            $originalImage = $request->file('img');
            foreach ($originalImage as $file) {//this statement will loop through all files.
                $file_name = $file->getClientOriginalName(); //Get file original name
                
                $file->move(public_path().'/notes' , $notes_image = 'img_'.Str::random(15).'.'.$file->getClientOriginalExtension()); // move files to destination folder
                $notes->image = $notes_image;
            }
            
        }
        $notes->save();
        // DB::table('booking_notes')->insert(
        //     [   
        //         'booking_id' => $request->id,
        //         'details' => $request->details,
              
        //     ]
        // );
        
         
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        if($request->id!=""){
            $notes = BookingNotes:: find($request->id);
            $notes->details = $request->details;
             if($request->hasFile('img')){
     
                 $originalImage = $request->file('img');
                 foreach ($originalImage as $file) {//this statement will loop through all files.
                     $file_name = $file->getClientOriginalName(); //Get file original name
                     
                     $file->move(public_path().'/notes' , $notes_image = 'img_'.Str::random(15).'.'.$file->getClientOriginalExtension()); // move files to destination folder
                     $notes->image = $notes_image;
                 }
                 
             }
             $notes->save();

        }
       
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
 
        DB::table('booking_notes')->where('id', $id)->delete();
       return response()->json(['success'=>'notes deleted successfully']);
    }
    
}
