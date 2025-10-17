@extends('layouts.admin')
@section('title', 'Edit Supplier | ' . Session::get('site_name'))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Edit Supplier</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Supplier</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <form class="forms-sample" action="{{ route('suppliers.update', $supplier->id) }}" method="post">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="name">Supplier Name</label>
                                <input type="text" class="form-control" name="name" value="{{ old('name', $supplier->name ?? '') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', $supplier->email ?? '') }}">
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address', $supplier->address ?? '') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                            <a class="btn btn-light" href="{{ route('suppliers.index') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
