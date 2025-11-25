
@extends('layouts.admin')
@section('title', 'Create New Supplier | ' . Session::get('site_name'))

@section('pagescript')
    <script src="{{ asset('js/select2.js') }}"></script>
@stop

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Create Award</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('awards.index') }}">Awards</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Award</li>
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
                        <h4 class="card-title">Awardee Details</h4>
                        <form class="forms-sample" action="{{ route('awards.update', $award->id) }}" method="post">
                            @csrf
                            @method('put')

                            <div class="form-group">

                              <label>Employee</label>

                              <select id="name" name="user_id" class="js-example-basic-single w-100">

                                <option value="" disabled selected>Please Select Awardee Name</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $award->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach

                              </select>

                            </div>

                            <div class="form-group">
                                <label for="award">Award</label>
                                <input type="text" class="form-control" name="award" value="{{ old('award', $award->award) }}" placeholder="Award">
                            </div>

                            <div class="form-group">
                                <label for="gift">Award Gift</label>
                                <input type="gift" class="form-control" name="gift" value="{{ old('gift', $award->gift) }}" placeholder="Gift">
                            </div>

                            <div class="form-group">
                                <label for="date">Award Date</label>
                              <div id="datepicker-popup-new" class="input-group date datepicker">
                                <input type="text" name="date" value="{{ old('date', $award->date) }}" class="form-control" required>
                                <span class="input-group-addon input-group-append border-left">
                                  <span class="far fa-calendar input-group-text"></span>
                                </span>
                              </div>
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
