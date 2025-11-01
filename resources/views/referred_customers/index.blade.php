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







        <div class="alert alert-success" style="display:none;" id="status-success"></div>







        @if(Session::has('success'))



        <div class="alert alert-success alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



            {{Session::get('success')}}



        </div>







        @elseif(Session::has('destroy'))



        <div class="alert alert-danger alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



            {{Session::get('destroy')}}



        </div>



        @endif







        @if(Session::has('update'))



        <div class="alert alert-success alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



            {{Session::get('update')}}



        </div>



        @endif






        <div class="card">



            <div class="card-body">


            <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="{{ route('inventories.index') }}">
                                @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <select name="per_page" id="per_page" onchange="this.form.submit()" class="form-control">
                                    @foreach($paginationOptions as $option)
                                        <option value="{{ $option }}" {{ request('per_page', auth()->user()->getPerPagePreference()) == $option ? 'selected' : '' }}>{{ $option }} per page</option>
                                    @endforeach
                                </select>
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






                            <a href="{{ route('referred_customers.create') }}" class="btn btn-primary btn-sm">Add new Source</a>





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



                                    @foreach($referreds as $referred)



                                    



                                    <tr>

                                    

                                        <td>{{$num}}</td>

                                        <td>
                                        {{ $referred->referred_by ?? 'N/A' }}
                                            
                                        </td>

                                        <td>{{$referred->created_at}}</td>


                                        <td>

                                            <a href="{{route('referred_customers.edit', ['referred_customer' => $referred->id])}}">



                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>



                                            </a>





                                            |
                                            <a href="#" onclick="event.preventDefault(); 
                                                                if (confirm('Are you sure to delete?')) {
                                                                    document.getElementById('delete-form-{{ $referred->id }}').submit();
                                                                }">
                                                <i class="fas fa-trash text-danger"></i>
                                            </a>


                                            <form id="delete-form-{{ $referred->id }}" action="{{ route('referred_customers.destroy', $referred->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>






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



function changeCategoryStatus(_this, id) {



 



    var status = $(_this).prop('checked') == true ? 1 : 0;



    $.ajax({



        url: "{{url('category-status')}}",



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