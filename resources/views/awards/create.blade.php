


@extends('layouts.admin')

@section('title', 'Create New Supplier | ' . (auth()->user()->companySetting->site_name ?? ''))

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

        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Awardee Details</h4>
                        <form class="forms-sample" action="{{ route('awards.store') }}" method="post">
                            @csrf

                            <div class="form-group">
                                <label for="user_id">Employee <span class="text-danger">*</span></label>

                                <select id="user_id"
                                        name="user_id"
                                        class="form-control @error('user_id') is-invalid @enderror js-example-basic-single w-100"
                                        style="width: 100%;">
                                    <option value="" disabled {{ old('user_id') ? '' : 'selected' }}>
                                        Please Select Awardee Name
                                    </option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('user_id')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @else
                                    <small class="text-muted">Select the employee who received the award</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="award">Award</label>
                                <input type="text" class="form-control @error('award') is-invalid @enderror" name="award" value="{{ old('award') }}" placeholder="Employee of the month" >
                                @error('award')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="gift">Award Gift</label>
                                <input type="gift" class="form-control @error('gift') is-invalid @enderror" name="gift" value="{{ old('gift') }}" placeholder="500" >
                                @error('gift')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="date">Award Date</label>
                              <div id="datepicker-popup-new" class="input-group date datepicker ">
                                <input type="text" name="date" value="{{ old('date') }}" class="form-control @error('date') is-invalid @enderror" >
                                <span class="input-group-addon input-group-append border-left">
                                  <span class="far fa-calendar input-group-text"></span>
                                </span>
                                @error('date')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
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
