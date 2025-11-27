{{-- resources/views/admin/roles/edit.blade.php --}}
@extends('layouts.admin')

@section('title', 'Edit Role | '. (auth()->user()->companySetting->site_name ?? ''))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Edit Role: {{ ucfirst($role->name) }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('roles.update', $role) }}" method="POST">
                            @csrf @method('PUT')

                            <div class="form-group">
                                <label>Role Name</label>
                                <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Permissions</label>
                                <div class="row">
                                    @foreach($permissions as $module => $perms)
                                        <div class="col-md-3">
                                            @foreach($perms as $perm)
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                                                               {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}
                                                               class="form-check-input">
                                                        {{ ucfirst(str_replace('_', ' ', $perm->name)) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Update Role</button>
                            <a href="{{ route('roles.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection