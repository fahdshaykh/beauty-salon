@extends('layouts.admin')



@section('title', 'Expense Types | '.Session::get('site_name'))



@section('content')

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                All Expense Types

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('expense-types')}}">Expense Types</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Expense Type Listing</li>

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





        <div class="col-lg-12 grid-margin stretch-card">

      

              <div class="card">

              <!-- <a class="btn btn-primary btn-fw {{ Request::is('expenses*') ? 'active' : '' }}" href="{{url('expense-types/creat')}}"> Add Expense Type</a> -->

                <div class="card-body">

                  <h4 class="card-title" style="float:right;"><a class="btn btn-primary btn-fw" href="{{url('expense-types/create')}}"> Add Expense Type</a></h4>

                  <div class="table-responsive">

                    <table class="table table-hover">

                      <thead>

                        <tr>

                          <th>Sr No#</th>

                          <th>Type</th>

                          <th>Status</th>

                          <th>Action</th>

                        </tr>

                      </thead>

                      <tbody>

                      <?php $num = 1; ?>

                    

                        @forelse($expense_types as $expense_type)

                        <tr>

                            <td>{{$num}}</td>

                            <td>{{$expense_type->type}}</td>

                            <td>

                                <label class="switch">

                                    <input type="checkbox" id="user-{{$expense_type->id}}" onclick="changeCategoryStatus(event.target, {{ $expense_type->id }});" {{($expense_type->is_active) ? 'checked' : ''}}>

                                    <span class="slider round"></span>

                                </label></td>

                            <td>

                            @can('edit expense types')
                                <a href="{{url('expense-types/edit',$expense_type->id)}}">

                                    <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>                          

                                </a>
                            @endcan


                           

                            </td>

                        </tr>

                        @empty

                        <tr><td colspan='3'> No Data Available</td></tr>

                        <?php $num++; ?>

                        @endforelse

                 

                      </tbody>

                    </table>

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



        url: "{{url('expense-type-status')}}",



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