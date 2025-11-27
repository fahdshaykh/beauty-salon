@extends('layouts.admin')

@section('title', 'Create Role | '. (auth()->user()->companySetting->site_name ?? ''))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Create Role</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Create Role</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Manager">
                            </div>

                            <div class="form-group">
                                <label>Permissions</label>
                                <div class="row">
                                    @foreach(\Spatie\Permission\Models\Permission::all() as $permission)
                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" class="form-check-input">
                                                    {{ ucfirst(str_replace('_', ' ', $permission->name)) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Create Role</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection