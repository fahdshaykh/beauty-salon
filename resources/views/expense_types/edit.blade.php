@extends('layouts.admin')



@section('title', 'Expense Type Edit | '.Session::get('site_name'))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Update Expense Type

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('expense_types/index')}}">Expense Types</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Edit Type</li>

                </ol>

            </nav>

        </div>

        <div class="row">

            <div class="col-md-6 grid-margin stretch-card">

                <div class="card">

                    <div class="card-body">

                        <h4 class="card-title">Category Edit Form</h4>

                        <form class="forms-sample" action="{{url('expense-types/update',$expense_type->id)}}" method="post">

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