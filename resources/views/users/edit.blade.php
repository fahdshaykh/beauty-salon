@extends('layouts.admin')

@section('title', 'Edit User | '.Session::get('site_name'))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Update User</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
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
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update User</h4>
                        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="forms-sample">
                            @csrf @method('PUT')

                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="form-group">
                                <label>Password <small>(leave blank to keep current)</small></label>
                                <input type="password" name="password" class="form-control" placeholder="New password">
                            </div>

                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" maxlength="15">
                            </div>

                            <div class="form-group">
                                <label>Base Salary</label>
                                <input type="number" name="base_salary" class="form-control" value="{{ old('base_salary', $user->base_salary) }}" step="0.01">
                            </div>

                            <div class="form-group">
                                <label>Commission Rate (%)</label>
                                <input type="number" name="commission_rate" class="form-control" value="{{ old('commission_rate', $user->commission_rate) }}" step="0.01">
                            </div>

                            <div class="form-group">
                                <label>Transport Allowance?</label>
                                <select name="is_transport" id="transportSelect" class="form-control" required>
                                    <option value="no" {{ old('is_transport', $user->is_transport) === 'no' ? 'selected' : '' }}>No</option>
                                    <option value="yes" {{ old('is_transport', $user->is_transport) === 'yes' ? 'selected' : '' }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group" id="transportAmount" style="display: {{ old('is_transport', $user->is_transport) === 'yes' ? 'block' : 'none' }};">
                                <label>Transport Allowance Amount</label>
                                <input type="number" name="transport_allowance" class="form-control" value="{{ old('transport_allowance', $user->transport_allowance) }}" step="0.01">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
                            </div>

                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}"
                                            {{ old('role', $user->getRoleNames()->first()) === $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Current Image</label><br>
                                @if($user->image)
                                    <img src="{{ asset('profiles/' . $user->image) }}" width="80" class="img-thumbnail">
                                @else
                                    <span class="text-muted">No image</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label>Update Image</label>
                                <input type="file" name="image" class="file-upload-default">
                                <div class="input-group">
                                    <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                                    <span class="input-group-append">
                                        <button class="file-upload-browse btn btn-primary" type="button">Upload</button>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Update User</button>
                            <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#transportSelect').on('change', function() {
        $('#transportAmount').toggle(this.value === 'yes');
    });
</script>
@endsection