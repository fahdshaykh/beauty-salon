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



                All Inventories



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{route('inventories.index')}}">Inventories</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Inventory Listing</li>



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



                    <h4 class="card-title">Inventory Listing</h4>



                    </div>



                    <div class="col-md-6"></div>



                    <div class="col-md-3">



                        <div class="text-center" style="float: center;">

                        @can('create inventories')

                            <a href="{{url('inventories/create')}}" class="btn btn-primary btn-sm">Add Item</a>

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

                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    @foreach($inventories as $inventory)



                                    



                                    <tr>

                                    

                                        <td>{{$num}}</td>

                                        <td>
                                        {{ $inventory->name ?? 'N/A' }}
                                            
                                        </td>

                                        <td>{{$inventory->created_at}}</td>

                                        <td>
                                            <div class="d-flex gap-1">
                                                @can('edit inventories')
                                                <a href="{{ route('inventories.edit', $inventory->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit inventory">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                @endcan


                                                @can('delete inventories')
                                                <a href="javascript:void(0)"
                                                onclick="if(confirm('Are You Sure Want To Delete?')) 
                                                            document.getElementById('delete-cat-{{ $inventory->id }}').submit();"
                                                class="btn btn-sm btn-outline-danger ml-1" title="Delete inventory">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>

                                                <form id="delete-cat-{{ $inventory->id }}"
                                                    action="{{ route('inventories.destroy', $inventory->id) }}"
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
                        <p>Showing {{ $inventories->firstItem() }} to {{ $inventories->lastItem() }} of {{ $inventories->total() }} entries</p>
                        {{ $inventories->links() }}
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
            window.location = "{{ url('/inventories') }}?rows=" + this.value;
        });
    }
});
</script>

    <script>



    function changeUserStatus(_this, id) {







        var status = $(_this).prop('checked') == true ? 1 : 0;



        $.ajax({



            url: "{{url('user-status')}}",



            type: 'GET',



            data: {



                id: id,



                status: status



            },



            success: function(result) {



                $("#status-success").show();



                $("#status-success").html(result.success);



                setTimeout(function() {



                    $("#status-success").hide()



                }, 3000);



            }



        });



    }



    </script>