@extends('layouts.admin')
@section('title', 'Template | B Saloon')

@section('content')
<div class="main-panel">
        <div class="content-wrapper">
          
            <div class="page-header">
                <h3 class="page-title">
                    Templates
                </h3>

                @can('create templates')
                <a style="float:right;" href="{{route('template.create')}}" class="btn btn-primary btn-add-template mb-2"> Add SMS Template </a>
                @endcan
            </div>
          
          <div class="card">

            <div class="card-body">
               
                <h4 class="card-title"></h4>
               
                <div class="row">
                
                    <div class="col-12">
                        
                        <div class="table-responsive">
                                
                            <table id="order-listing" class="table table-striped table-bordered">

                                <thead>

                                    <tr class="bg-primary text-white">
                                       
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data as $templates)
                                 
                                    <?php
                                    $type = '';
                                   
                                    switch ($templates->type) {
                                      
                                        case 1:
                                            $type = 'SMS';
                                            break;
                                        case 2:
                                            $type = 'WhatsApp';
                                            break;
                                        default:
                                        $type = 'Email';
                                        }
                                        ?>
                                        <tr>
                                            <td>
                                                {{$type}}
                                            </td>
                                            <td>
                                                {{$templates->title}}
                                            </td>
                                            <td>
                                                {{$templates->description}}
                                            </td>
                                            <td>
                                            <label class="switch">
                                                <input type="checkbox" id="user--{{$templates->id}}"
                                                    onclick="changeTemplateStatus(event.target, {{ $templates->id }})" {{($templates->status) ? 'checked' : ''}}>

                                                <span class="slider round"></span>

                                            </label>
  
                                        </td>
                                            <td>
                                            
                                            @can('edit templates')
                                            <a href="{{Route('template.edit',$templates->id)}}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            @endcan
                                             
                                            </td>

                                        </tr>


                                    @empty

                                    <tr style="text-align: center">
                                        <td colspan='6'> No Data Available</td>
                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

<script>
function changeTemplateStatus(_this, id) {
    var status = $(_this).prop('checked') == true ? 1 : 0;
    $.ajax({

        url: "{{url('template-status')}}",

        type: 'GET',

        data: {

            id: id,

            status: status 

        },

        success: function (result) {

          $("#status-success").show();

          $("#status-success").html(result.success);

          setTimeout(function() {

            $("#status-success").hide()

          }, 3000);

        }

    });

}
</script>
</div>
</div>
@endsection



