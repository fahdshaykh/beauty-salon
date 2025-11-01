@extends('layouts.admin')

@section('title', 'Update Referred Source | '.Session::get('site_name'))

@section('content')

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Update Referred Source</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('referred_customers.index') }}">Referred Sources</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Update Referred Source</h4>
                        <form class="forms-sample" action="{{ route('referred_customers.update', $referred->id) }}" method="POST">
                            @csrf
                            @method('put')

                            <div class="form-group">
                                <label for="referred_by">Source name</label>
                                <input type="text" class="form-control" id="referred_by" name="referred_by" value="{{ old('referred_by', $referred->referred_by) }}" placeholder="e.g. Google">
                            </div>

                            <div class="form-group">
                                <label for="employees">Status</label>
                                
                                <select name="is_active" class="form-control" required>
                                    <option value="1" {{ ($referred->is_active == 1) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ ($referred->is_active == 0) ? 'selected' : '' }}>In-active</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Update</button>
                            <a class="btn btn-light" href="{{ route('referred_customers.index') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
