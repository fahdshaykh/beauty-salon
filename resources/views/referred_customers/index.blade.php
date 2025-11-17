@extends('layouts.admin')







@section('title', 'Users | '.Session::get('site_name'))







@section('content')



<style>



.switch {



    position: relative;



    display: inline-block;



    width: 60px;



    height: 34px;



}







.switch input {



    opacity: 0;



    width: 0;



    height: 0;



}







.slider {



    position: absolute;



    cursor: pointer;



    top: 0;



    left: 0;



    right: 0;



    bottom: 0;



    background-color: #ccc;



    -webkit-transition: .4s;



    transition: .4s;



}







.slider:before {



    position: absolute;



    content: "";



    height: 26px;



    width: 26px;



    left: 4px;



    bottom: 4px;



    background-color: white;



    -webkit-transition: .4s;



    transition: .4s;



}







input:checked+.slider {



    background-color: #392C70;



}







input:focus+.slider {



    box-shadow: 0 0 1px #2196F3;



}







input:checked+.slider:before {



    -webkit-transform: translateX(26px);



    -ms-transform: translateX(26px);



    transform: translateX(26px);



}







/* Rounded sliders */



.slider.round {



    border-radius: 34px;



}







.slider.round:before {



    border-radius: 50%;



}



</style>



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                All Referred Sources



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{route('users.index')}}">Referreds</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Referred Resources</li>



                </ol>



            </nav>



        </div>


        <div class="card">



            <div class="card-body">


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



                    <div class="col-md-3">



                    <h4 class="card-title">Referred Sources</h4>



                    </div>



                    <div class="col-md-6"></div>



                    <div class="col-md-3">



                        <div class="text-center" style="float: center;">






                        @can('create referred customers')
                            <a href="{{ route('referred_customers.create') }}" class="btn btn-primary btn-sm">Add new Source</a>
                        @endcan



                        </div>



                    </div>



                </div>



                <div class="row">



                    <div class="col-12">



                        <div class="table-responsive">



                            <table class="table">



                                <thead>



                                    <tr>



                                        <th>Sr.No #</th>

                                        <th>Name</th>

                                        <th>Created Date</th>

                                        <th>Status</th>

                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    @foreach($referreds as $referred)



                                    



                                    <tr>

                                    

                                        <td>{{$num}}</td>

                                        <td>
                                        {{ $referred->referred_by ?? 'N/A' }}
                                            
                                        </td>

                                        <td>{{$referred->created_at}}</td>

                                        <td>
                                            <label class="switch">
                                                <input type="checkbox"
                                                    id="user-{{ $referred->id }}"
                                                    data-name="{{ $referred->referred_by }}"
                                                    onclick="changeReferredStatus(this, {{ $referred->id }})"
                                                    {{ $referred->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-1">
                                                @can('edit referred customers')
                                                <a href="{{route('referred_customers.edit', ['referred_customer' => $referred->id])}}"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan


                                                @can('delete referred customers')
                                                <a href="javascript:void(0)"
                                                onclick="if(confirm('Are You Sure Want To Delete?')) 
                                                            document.getElementById('delete-cat-{{ $referred->id }}').submit();"
                                                class="btn btn-sm btn-outline-danger ml-1" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>

                                                <form id="delete-cat-{{ $referred->id }}"
                                                    action="{{ route('referred_customers.destroy', $referred->id) }}"
                                                    method="POST" style="display:none;">
                                                    @csrf @method('DELETE')
                                                </form>
                                                @endcan

                                            </div>
                                        </td>



                                    </tr>



                                    <?php $num++; ?>




                                    @endforeach



                                </tbody>



                            </table>



                        </div>



                    </div>



                </div>


                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing {{ $referreds->firstItem() }} to {{ $referreds->lastItem() }} of {{ $referreds->total() }} entries</p>
                        {{ $referreds->links() }}
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
            window.location = "{{ url('/referred_customers') }}?rows=" + this.value;
        });
    }
});
</script>

<script>



function changeReferredStatus(_this, id) {

    var $checkbox = $(_this);
    var status    = $checkbox.prop('checked') ? 1 : 0;
    var catName   = $checkbox.data('name'); // Get category name

    $.ajax({
        url: "{{ url('referred-status') }}",
        type: 'GET',
        data: { id: id, status: status },
        success: function (result) {
            // SUCCESS TOAST with Category Name
            iziToast.success({
                title: catName + (status ? ' Activated!' : ' Deactivated!'),
                message: 'Status updated successfully.',
                position: 'topRight',
                icon: 'fas fa-check-circle',
                timeout: 5000
            });
        },
        error: function () {
            // Revert checkbox on error
            $checkbox.prop('checked', !status);

            iziToast.error({
                title: catName + ' – Update Failed!',
                message: 'Could not update status. Please try again.',
                position: 'topRight',
                icon: 'fas fa-exclamation-triangle',
                timeout: 6000
            });
        }
    });

}

</script>