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







        <?php $service_edit_array = array(2,3); ?>



        <?php $service_edit = implode(',', $service_edit_array); ?>



        <?php $service_delete_array = array(2,4); ?>



        <?php $service_delete = implode(',', $service_delete_array); ?>







        <div class="card">



            <div class="card-body">



                <h4 class="card-title"></h4>

                <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <label for="per_page">Items per page</label>
                            <form method="GET" action="{{ route('services.index') }}">
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



                                                <input type="checkbox" id="user-{{$service->id}}"



                                                    onclick="changeServiceStatus(event.target, {{ $service->id }});"



                                                    {{($service->is_active) ? 'checked' : ''}}>



                                                <span class="slider round"></span>



                                            </label>

                                        </td>

                                        <td>

                                        <div class="btn-group align-top">
                                        @if(in_array($service_edit, session()->get('all_rp')['0']))

                                            <a href="{{route('services.edit',$service->id)}}" class="btn btn-default">
                                                    <i class="fas fa-pen" style="font-size:15px;"></i>
                                            </a>

                                        @endif 


                                        @if(in_array($service_delete, session()->get('all_rp')['0']))

                                            <form method="post" action="{{ route('services.destroy', $service->id) }}" onsubmit="return confirm('Are You Sure Want To Delete?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-trash-alt" style="font-size:15px; color:red;"></i>
                                                </button>
                                            </form>

                                        @endif 

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

    function changeServiceStatus(_this, id) {







        var status = $(_this).prop('checked') == true ? 1 : 0;



        $.ajax({



            url: "{{url('servicestatus')}}",



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