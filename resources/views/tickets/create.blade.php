@section('pagescript')
    <script src="{{asset('js/file-upload.js')}}"></script>
    <script src="{{asset('js/typeahead.js')}}"></script>
    <script src="{{asset('js/select2.js')}}"></script>
    <script src="{{asset('js/formpickers.js')}}"></script>
@stop

@extends('layouts.admin')

@section('title', 'Create Support Ticket | '. (auth()->user()->companySetting->site_name ?? ''))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">Create New Support Ticket</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('tickets.index') }}">Tickets</a></li>
                    <li class="breadcrumb-item active">Create Ticket</li>
                </ol>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Ticket Details</h4>

                        <form class="forms-sample" action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="subject">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="priority">Priority <span class="text-danger">*</span></label>
                                <select name="priority" id="priority" class="js-example-basic-single w-100" required>
                                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" rows="8" class="form-control" required>{{ old('description') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="attachments">Attachments (optional)</label>
                                <input type="file" name="attachments[]" id="attachments" multiple class="form-control file-upload-info"
                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx,.zip">
                                <small class="text-muted">Max 10MB per file. You can select multiple files.</small>
                            </div>

                            <button type="submit" class="btn btn-primary mr-2">Submit Ticket</button>
                            <a href="{{ route('tickets.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
@endpush