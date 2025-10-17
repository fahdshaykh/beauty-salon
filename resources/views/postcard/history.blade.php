@extends('layouts.admin')
@section('title', 'PostCard History | '.Session::get('site_name'))
@section('content')
<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

               PostCard History

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('marketing')}}">Marketing</a></li>

                    <li class="breadcrumb-item active" aria-current="page">PostCard History</li>

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

                    <div class="row">

                            <div class="col-md-3">

                            <h4 class="card-title">PostCad History</h4>

                            </div>

                        <div class="col-md-6"></div>

                    </div>

                <div class="row">

                    <div class="col-12 mt-2">

                        <div class="table-responsive">

                            <table id="order-listing" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Price</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                     
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($history_detail->total>0)
                                        @foreach($history_detail->data as $hist)
                                            <tr>
                                                <td>{{$hist->address_name}}</td>
                                                <td>{{date("d-m-Y",$hist->date_added)}}</td>
                                                <td>£{{$hist->post_price}}</td>
                                                <td>{{$hist->address_line_1}},{{$hist->address_city}},{{$hist->address_postal_code}}</td>
                                                <td>{{$hist->status_text}}</td>
                                            </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="5">No History Available</td>
                                        </tr>
                                    @endif
                                  

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    @endsection
