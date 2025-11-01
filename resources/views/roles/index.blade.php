@extends('layouts.admin')

@section('title', 'Roles | '.Session::get('site_name'))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">All Roles</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                    <li class="breadcrumb-item active">Role Listing</li>
                </ol>
            </nav>
        </div>

        <!-- Success Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('success') }}
            </div>
        @endif

        @if(session('destroy'))
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('destroy') }}
            </div>
        @endif

        @if(session('update'))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ session('update') }}
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <!-- Pagination Dropdown -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <form method="GET" action="{{ route('roles.index') }}">
                            @foreach(request()->except(['page', 'per_page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="per_page" onchange="this.form.submit()" class="form-control">
                                @foreach([10, 25, 50, 100] as $option)
                                    <option value="{{ $option }}" {{ request('per_page', 10) == $option ? 'selected' : '' }}>
                                        {{ $option }} per page
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-3 text-right">
                        @can('create roles')
                            <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">
                                Add New Role
                            </a>
                        @endcan
                    </div>
                </div>

                <!-- Roles Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sr.No #</th>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $index => $role)
                                @if($role->name !== 'admin') <!-- Optional: hide admin -->
                                <tr>
                                    <td>{{ $roles->firstItem() + $index }}</td>
                                    <td><strong>{{ ucfirst($role->name) }}</strong></td>
                                    <td>
                                        @foreach($role->permissions->take(3) as $perm)
                                            <span class="badge badge-info mr-1">
                                                {{ ucfirst(str_replace('_', ' ', $perm->name)) }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="text-muted">+{{ $role->permissions->count() - 3 }} more</span>
                                        @endif
                                    </td>
                                    <td>{{ $role->created_at->format('m-d-Y') }}</td>
                                    <td>
                                        @can('edit roles')
                                            <a href="{{ route('roles.edit', $role) }}" class="text-warning mr-2">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan

                                        @can('delete roles')
                                            @if($role->name !== 'admin')
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="border-0 bg-transparent text-danger"
                                                            onclick="return confirm('Delete {{ $role->name }} role?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No roles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row mt-4">
                    <div class="col-12">
                        <p class="text-muted">
                            Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} entries
                        </p>
                        {{ $roles->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection