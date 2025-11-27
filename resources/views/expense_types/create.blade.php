@extends('layouts.admin')







@section('title', 'Expense Type Create | '. (auth()->user()->companySetting->site_name ?? ''))







@section('content')







<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                Create Expense Type



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{url('expense-types')}}">Expense Types</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Create Type</li>



                </ol>



            </nav>



        </div>



        @if(Session::has('error'))



        <div class="alert alert-danger alert-dismissible">



            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



            {{Session::get('error')}}



        </div>



        @endif



        <div class="row">



            <div class="col-md-6 grid-margin stretch-card">



                <div class="card">



                    <div class="card-body">



                        <h4 class="card-title">Expense Type Form</h4>



                        <form class="forms-sample" action="{{url('expense-types/store')}}" method="post">



                            @csrf



                            @include('expense_types.expense_type_form')



                        </form>



                    </div>



                </div>



            </div>



        </div>



    </div>



</div>



@endsection