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



                All Users



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{route('users.index')}}">Users</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Users Listing</li>



                </ol>



            </nav>



        </div>



        <div class="card">



            <div class="card-body">



                        <div class="row">



                            <div class="col-md-3">



                            <h4 class="card-title">User Listing</h4>



                            </div>



                        <div class="col-md-6"></div>



                        <div class="col-md-3">



                            <div class="text-center" style="float: center;">






                                <a href="{{url('user/user-create')}}" class="btn btn-primary btn-sm">Add New User</a>






                            </div>



                        </div>



                        </div>


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



                            <table class="table">



                                <thead>



                                    <tr>



                                        <th>Sr.No #</th>



                                        <th>User Name</th>

                                        <th>User Email</th>

                                        <th>User phone</th>

                                        <th>User address</th>

                                        <th>Status</th>



                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    @foreach($users as $user)



                                        @role('admin')
        @if($user->id != auth()->id())



                                    <tr>



                                        <td>{{$num}}</td>






                                        <td> {{$user->name}} </td>

                                        <td>{{$user->email}}</td>

                                        <td>{{$user->phone}}</td>

                                        <td>{{$user->address}}</td>

                                        <td>
                                            <label class="switch">
                                                <input type="checkbox"
                                                    id="user-{{ $user->id }}"
                                                    data-name="{{ $user->name }}"
                                                    onclick="changeUserStatus(this, {{ $user->id }})"
                                                    {{ $user->is_active ? 'checked' : '' }}>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-1">

                                                
                                                <a href="{{url('user/edit',$user->id)}}"
                                                class="btn btn-sm btn-outline-primary" title="Edit Booking">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>

                                                
                                                <a href="{{url('user/destroy',$user->id)}}"
                                                onclick="return confirm('Are you sure to delete?')"
                                                class="btn btn-sm btn-outline-danger ml-1" title="Delete Booking">
                                                    <i class="fas fa-trash"></i>
                                                </a>

                                                
                                                <a href="{{url('user/payslip',$user->id)}}"
                                                class="btn btn-sm btn-outline-dark ml-1" title="View / Send Invoice">
                                                <i class='fas fa-file-pdf' style='font-size:14px'></i>
                                                </a>

                                            </div>

                                        </td>



                                    </tr>



                                    <?php $num++; ?>



                                            @endif
    @endrole



                                    @endforeach



                                </tbody>



                            </table>



                        </div>



                    </div>



                </div>

                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries</p>
                        {{ $users->links() }}
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
            window.location = "{{ url('/users') }}?rows=" + this.value;
        });
    }
});
</script>


<script>
function changeUserStatus(_this, id) {
    var $checkbox = $(_this);
    var status    = $checkbox.prop('checked') ? 1 : 0;
    var userName  = $checkbox.data('name'); // Get user name

    $.ajax({
        url: "{{ url('user-status') }}",
        type: 'GET',
        data: { id: id, status: status },
        success: function (result) {
            iziToast.success({
                title: userName,
                message: result.success || 'User status updated successfully.',
                position: 'topRight',
                icon: status ? 'fas fa-check-circle' : 'fas fa-ban',
                timeout: 5000
            });
        },
        error: function () {
            // Revert checkbox on error
            $checkbox.prop('checked', !status);

            iziToast.error({
                title: userName + ' – Update Failed!',
                message: 'Could not update status. Please try again.',
                position: 'topRight',
                icon: 'fas fa-exclamation-triangle',
                timeout: 6000
            });
        }
    });
}
</script>