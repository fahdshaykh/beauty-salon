@extends('layouts.admin')

@section('title', 'Template | '. (auth()->user()->companySetting->site_name ?? ''))


@section('content')

<div class="main-panel">

        <div class="content-wrapper">

          <div class="page-header">

            <h3 class="page-title">

             Templates

            </h3>

            @can('create email templates')
            <a style="float:right;" href="{{url('/email-template-create')}}" class="btn btn-primary btn-add-template mb-2"> Add Email Template</a>
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



                                    <tr>

                                    

                                        <th>Title</th>

                                        <th>Status</th>

                                        <th>Action</th>



                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($data as $templates)

                                        <tr>

                                            <td>

                                                {{$templates->title}}

                                            </td>

                                          

                                            <td>
                                                <label class="switch">
                                                    <input type="checkbox"
                                                        id="template-{{ $templates->id }}"
                                                        data-name="{{ $templates->title }}"
                                                        onclick="changeTemplateStatus(this, {{ $templates->id }})"
                                                        {{ $templates->status ? 'checked' : '' }}>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>

                                            <td>


                                            <a href="{{url('/email-template-edit',$templates->id)}}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                             
                                            <a target="_blank" href="{{url('get-template-preview',$templates->id)}}"
                                            class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fa fa-eye text-primary"></i>
                                            </a>


                                            </td>



                                        </tr>





                                    @endforeach

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
function changeTemplateStatus(_this, id) {
    var $checkbox = $(_this);
    var status    = $checkbox.prop('checked') ? 1 : 0;
    var templateName = $checkbox.data('name'); // Get template name

    $.ajax({
        url: "{{ url('template-status') }}",
        type: 'GET',
        data: { id: id, status: status },
        success: function (result) {
            iziToast.success({
                title: templateName + (status ? ' Activated!' : ' Deactivated!'),
                message: result.success || 'Template status updated successfully.',
                position: 'topRight',
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
                position: 'topRight',
                icon: 'fas fa-exclamation-triangle',
                timeout: 6000
            });
        }
    });
}
</script>






