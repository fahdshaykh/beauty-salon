@extends('layouts.admin')

@section('title', 'Create New Supplier | ' . (auth()->user()->companySetting->site_name ?? ''))

@section('pagescript')
    <script src="{{ asset('js/select2.js') }}"></script>
@stop

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Create Supplier</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Supplier</li>
                </ol>
            </nav>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Supplier Details</h4>
                        <form class="forms-sample" action="{{ route('suppliers.store') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="name">Supplier Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="Enter Supplier Name">
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="Phone">
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email">
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" rows="3" placeholder="Address"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Submit</button>
                            <a class="btn btn-light" href="{{ route('suppliers.index') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
