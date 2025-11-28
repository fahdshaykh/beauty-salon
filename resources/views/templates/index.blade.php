@extends('layouts.admin')
    
@section('title', 'Template | '. (auth()->user()->companySetting->site_name ?? ''))

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
                                                    data-name="{{ $templates->title }}"
                                                    onclick="changeSMSTemplateStatus(event.target, {{ $templates->id }})" {{($templates->status) ? 'checked' : ''}}>

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
    </div>
</div>
@endsection

<script>
function changeSMSTemplateStatus(_this, id) {

    var $checkbox = $(_this);
    var status    = $checkbox.prop('checked') ? 1 : 0;
    var templateName = $checkbox.data('name'); // Get template name

    $.ajax({

        url: "{{url('template-status')}}",

        type: 'GET',

        data: {

            id: id,

            status: status 

        },

        success: function (result) {
            iziToast.success({
                title: templateName + (status ? ' Activated!' : ' Deactivated!'),
                message: result.success || 'Template status updated successfully.',
                position: 'bottomRight',
                icon: status ? 'fas fa-check-circle' : 'fas fa-ban',
                timeout: 5000
            });
        },
        error: function () {
            // Revert checkbox on error
            $checkbox.prop('checked', !status);

            iziToast.error({
                title: templateName + ' – Update Failed!',
                message: 'Could not update status. Please try again.',
                position: 'bottomRight',
                icon: 'fas fa-exclamation-triangle',
                timeout: 6000
            });
        }

    });

}
</script>
