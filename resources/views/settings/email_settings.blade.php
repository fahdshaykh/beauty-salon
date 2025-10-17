@extends('layouts.admin')

@section('title', 'Email Settings | '.Session::get('site_name'))

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Customize E-mail
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('message-setting')}}">Email Setting</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Custom E-mail</li>
                </ol>
            </nav>
        </div>

        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{Session::get('success')}}
        </div>
        @endif

        <div class="alert alert-success" style="display:none;" id="status-success"></div>

        <div class="card">
            <div class="card-body">
                    <div class="row">
                            <div class="col-md-3">
                            <h4 class="card-title"></h4>
                            </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-3">
                            <div class="text-center">
                                
                            </div>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-12 mt-3">
                        <div class="table-responsive">
                            <table id="order-listing" class="table">
                                <thead>
                                    <tr>
                                        <th>E-Mail</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{$email['0']->email}}</td>
                                        <td>
                                            <a href="{{url('email-settings/edit',$email['0']->company_id)}}">
                                                <i class="fas fa-pencil-alt text-primary" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection