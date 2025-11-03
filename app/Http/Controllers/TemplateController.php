<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Templates;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (! (auth()->user() && auth()->user()->can('view templates'))) {
            abort(403);
        }

        $data =Templates::select('*')->where('type', 1)->where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->get();
        return view('templates.index',compact('data'));
    }

    public function email_templates()
    {
        if (! (auth()->user() && auth()->user()->can('view templates'))) {
            abort(403);
        }

        $data =Templates::select('*')->where('type', 3)->where('company_id', auth()->user()->company_id)->orderBy('created_at', 'desc')->get();
        return view('email_templates.index',compact('data'));
    }

    public function email_template_create()
    { 
        if (! (auth()->user() && auth()->user()->can('create templates'))) {
            abort(403);
        }
        return view('email_templates.create');
    }

    public function store_email_template(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('create templates'))) {
            abort(403);
        }

        $this->validate($request, 
            [
                'title'         => 'required',
                'email_des'   => 'required'
            ]

        );

        $data=[
            'type' => 3,
            'title' => $request->title,
            'description' => html_entity_decode($request->email_des),
            'template_json' => $request->design
       
        ];
        $data['company_id'] = auth()->user()->company_id; 
       
        Templates::create($data);
        return response()->json(['success' => 'Template created successfully']);
        //return back()->with('success','Email Template Created Successfully!');
        //return redirect('/template')->with('success', 'Template created successfully');
    }

    public function edit_email_template($id)
    {
        if (! (auth()->user() && auth()->user()->can('edit templates'))) {
            abort(403);
        }

        $template = Templates::where('id',$id)->first();
       
       
        return view('email_templates.edit',compact('template'));
    }

    public function get_email_template_json($id){
        $template = Templates::select('template_json')->where('id',$id)->first();
        //return response()->json([$template->template_json]);
        print_r($template->template_json); exit;
    }

    public function update_email_template(Request $request)
    {
        if (! (auth()->user() && auth()->user()->can('edit templates'))) {
            abort(403);
        }

        $this->validate($request, 

            [
                'title'         => 'required',
                'email_des'  => 'required'
            ]

        );


        $template = Templates::find($request->id);
            
        $template->id = $request->id;

        $template->type =3;

        $template->title = $request->title;

        $template->description = $request->email_des;
        $template->template_json = $request->design;
        

        $template->save();
        return response()->json(['success' => 'Template Updated successfully']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       if($request->message_des !==null){
      $description=$request->message_des;
       }
      else if($request->message_des==null && $request->email_des==null){
        $description=$request->whatsapp_des;
      }
      else{
        $description=$request->email_des;
      }
      $this->validate($request, 

        [
            'template_type' => 'required',
            'title'         => 'required',
        ]

    );
        $data=[
            'type' => $request->template_type,
            'title' => $request->title,
            'description' => $description,
       
        ];
        $data['company_id'] = auth()->user()->company_id; 
       
        Templates::create($data);

        return redirect('/template')->with('success', 'Template created successfully');
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
        $template = Templates::where('id',$id)->first();
       
       
        return view('templates.edit',compact('template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, 

        [
            'template_type' => 'required',
            'title'         => 'required',
            'description'  => 'required'
        ]

    );


    $template = Templates::find($id);
        
    $template->id = $id;

    $template->type = $request->template_type;

    $template->title = $request->title;

    $template->description = $request->description;
    

    $template->save();
    return redirect('/template')->with('update','Template Updated');   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    
    public function templateStatus(Request $request)

    {
        

        $template = Templates::find($request->id);

        $template->status = $request->status;

        $template->save();

        return response()->json(['success'=>'Status changed successfully.']);

       

    }

}
