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

        <div class="card">
            <div class="card-body">
                <!-- Pagination Dropdown -->
                <div class="row mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="pagination-dropdown">
                                <form class="form-inline">
                                    <div class="form-group">
                                        <label for="rows">Records:&nbsp;</label>
                                        <select class="form-control" id="rows" name="rows">
                                            <option value="5" @if($rows == 5) selected @endif > 5 </option>
                                            <option value="10" @if($rows == 10) selected @endif > 10 </option>
                                            <option value="20" @if($rows == 20) selected @endif > 20 </option>
                                            <option value="50" @if($rows == 50) selected @endif > 50 </option>
                                            <option value="100" @if($rows == 100) selected @endif > 100 </option>
                                            <option value="500" @if($rows == 500) selected @endif > 500 </option>                        
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
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

                                            <a href="{{ route('roles.edit', $role) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit Role">
                                                    <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        @endcan

                                        @can('delete roles')
                                            @if($role->name !== 'admin')
                                                <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger ml-1"
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsSelect = document.getElementById('rows');

    if (rowsSelect) {
        rowsSelect.addEventListener('change', function () {
            window.location = "{{ url('/roles') }}?rows=" + this.value;
        });
    }
});
</script>