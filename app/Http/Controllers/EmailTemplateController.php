<?php

namespace App\Http\Controllers;

use App\email_template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates =  email_template::get();
        return view('email_templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('email_templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([

            'template_name' => 'required',
            'template_id' => 'required',
            'template_preview' => 'required',
        ],[
            'template_name.required' => 'Please Enter Template Title',
            'template_id.required' => 'Please Enter Template ID',
            'template_preview.required' => 'Please Add Preview Image',
            
        ]);
        //dd($request->file()); 
        $input = $request->all();

        if ($image = $request->file('template_preview')) {
        
            $destinationPath =public_path('images/');
    
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['template_preview'] = "$profileImage";
        }
       
        email_template::create($input);
     
        return back()->with('success','Career created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\email_template  $email_template
     * @return \Illuminate\Http\Response
     */
    public function show(email_template $email_template)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\email_template  $email_template
     * @return \Illuminate\Http\Response
     */
    public function edit(email_template $email_template, $id)
    {
      
        $template=email_template::findOrFail($id);
        return view('email_templates.edit',compact('template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\email_template  $email_template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, email_template $email_template, $id)
    {
        $request->validate([

            'template_name' => 'required',
            'template_id' => 'required',
      
        ],[
            'template_name.required' => 'Please Enter Template Title',
            'template_id.required' => 'Please Enter Template ID',
        ]);
        //dd($request->file()); 
        $template=email_template::findOrFail($id);
        if ($image = $request->file('template_preview')) {
        
            $destinationPath =public_path('images/');
    
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $image = "$profileImage";
            if (File::exists("images/".$template->template_preview)) {
                File::delete("images/".$template->template_preview);
            }
        }
        else{
            $image = $template->template_preview;
        }
       
        $template->update([
            "template_name" =>$request->template_name,
            "template_id"=>$request->template_id,
            "template_preview" => $image
        ]);
     
        return back()->with('success','Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\email_template  $email_template
     * @return \Illuminate\Http\Response
     */
    public function destroy(email_template $email_template)
    {
        //
    }
    public function templateStatus(Request $request)

    {
        

        $template = email_template::find($request->id);

        $template->is_active = $request->status;

        $template->save();

        return response()->json(['success'=>'Status changed successfully.']);

       

    }
}
