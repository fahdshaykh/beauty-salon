@extends('layouts.admin')







@section('title', 'Services | '.Session::get('site_name'))







@section('content')



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                All Services



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{route('services.index')}}">Services</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Services Listing</li>



                </ol>



            </nav>



        </div>







        <div class="alert alert-success" style="display:none;" id="status-success"></div>


        <div class="card">



            <div class="card-body">



                <h4 class="card-title"></h4>

                <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <form class="form-inline">
                                <div class="form-group">
                                    <label for="rows">Records:&nbsp;</label>
                                    <select class="form-control" id="rows" name="rows">
                                        <option value="5" @if($rows == 5) selected @endif > 5 </option>
                                        <option value="10" @if($rows == 10) selected @endif > 10 </option>
                                        <option value="20" @if($rows == 20) selected @endif > 20 </option>
                                        <option value="50" @if($rows == 50) selected @endif > 50 </option>
                                        <option value="100" @if($rows == 100) selected @endif > 100 </option>
										<option value="500" @if($rows == 500) selected @endif > 500 </option>                        
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>



                <div class="row">



                    <div class="col-12">



                        <div class="table-responsive">



                            <table class="table table-striped">



                                <thead>



                                    <tr>

                                        <th>Category Name</th>

                                        <th>Service Name</th>

                                        <th>Service Price</th>

                                        <th>Created at</th>

                                        <th>Status</th>

                                        <th>Actions</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach($services as $service)

                                    <tr>

                                        <td>{{$service->categories->name}}</td>

                                        <td>{{$service->name}}</td>

                                        <td><i class="fas fa-dollar-sign"></i>{{$service->price}}</td>

                                        <td>{{date('m-d-Y', strtotime($service->created_at))}}</td>

                                        <td>
                                            <label class="switch">
                                                <input type="checkbox"
                                                    id="service-{{ $service->id }}"
                                                    data-name="{{ $service->name }}" 
                                                    onclick="changeServiceStatus(this, {{ $service->id }})"
                                                    {{ $service->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-1">
                                                @can('edit services')
                                                <a href="{{ route('services.edit', $service->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan


                                                @can('delete services')
                                                <a href="javascript:void(0)"
                                                onclick="if(confirm('Are You Sure Want To Delete?')) 
                                                            document.getElementById('delete-cat-{{ $service->id }}').submit();"
                                                class="btn btn-sm btn-outline-danger ml-1" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>

                                                <form id="delete-cat-{{ $service->id }}"
                                                    action="{{ route('services.destroy', $service->id) }}"
                                                    method="POST" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                                @endcan

                                            </div>
                                        </td>
                                        
                                    </tr>

                                    @endforeach

                                </tbody>



                            </table>



                        </div>



                    </div>



                </div>

                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of {{ $services->total() }} entries</p>
                        {{ $services->links() }}
                    </div>
                </div>



            </div>



        </div>



    </div>



    @endsection




<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsSelect = document.getElementById('rows');

    if (rowsSelect) {
        rowsSelect.addEventListener('change', function () {
            window.location = "{{ url('/services') }}?rows=" + this.value;
        });
    }
});
</script>


<script>
function changeServiceStatus(_this, id) {
    var $checkbox = $(_this);
    var status    = $checkbox.prop('checked') ? 1 : 0;
    var serviceName = $checkbox.data('name'); // Get service name

    $.ajax({
        url: "{{ url('servicestatus') }}",
        type: 'GET',
        data: { id: id, status: status },
        success: function (result) {
            iziToast.success({
                title: serviceName + (status ? ' Activated!' : ' Deactivated!'),
                message: result.success || 'Status updated successfully.',
                position: 'topRight',
                icon: status ? 'fas fa-check-circle' : 'fas fa-ban',
                timeout: 5000
            });
        },
        error: function () {
            // Revert checkbox on error
            $checkbox.prop('checked', !status);

            iziToast.error({
                title: serviceName + ' – Update Failed!',
                message: 'Could not update status. Please try again.',
                position: 'topRight',
                icon: 'fas fa-exclamation-triangle',
                timeout: 6000
            });
        }
    });
}
</script>