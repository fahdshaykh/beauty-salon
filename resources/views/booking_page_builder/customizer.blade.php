@extends('layouts.admin')

@section('title', 'Public Booking Page Customizer | ' . Session::get('site_name'))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title font-weight-bold">
                    <i class="fas fa-magic text-primary mr-2"></i> Public Booking Page Builder
                </h3>
                <p class="text-muted small mb-0">Customize your public single-page booking link, branding, and layout blocks</p>
            </div>
            <div>
                <a href="{{ url('/book/' . $setting->slug) }}" target="_blank" class="btn btn-outline-primary btn-icon-text mr-2">
                    <i class="fas fa-external-link-alt btn-icon-prepend"></i> View Live Page
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- Shareable Link Banner Card -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 14px; background: linear-gradient(135deg, #4b49ac 0%, #7978e9 100%); color: #ffffff;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <span class="badge badge-light text-primary font-weight-bold mb-2">Your Shareable Booking Link</span>
                        <h5 class="font-weight-bold mb-2">Share this link with your customers or on social media</h5>
                        <div class="input-group">
                            <input type="text" id="publicBookingUrl" class="form-control bg-white font-weight-bold" value="{{ url('/book/' . $setting->slug) }}" readonly>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-light font-weight-bold" id="btnCopyUrl">
                                    <i class="fas fa-copy mr-1 text-primary"></i> <span id="copyBtnText">Copy Link</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center text-md-right">
                        <div class="d-inline-block bg-white p-2 rounded shadow-sm text-center">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode(url('/book/' . $setting->slug)) }}" alt="QR Code" width="90" height="90">
                            <div class="small font-weight-bold text-dark mt-1">Scan to Book</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Customizer Form Column -->
            <div class="col-lg-7 grid-margin stretch-card">
                <div class="card shadow-sm border-0" style="border-radius: 14px;">
                    <div class="card-body">

                        <ul class="nav nav-tabs nav-fill mb-4" id="customizerTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active font-weight-bold" id="branding-tab" data-toggle="tab" href="#branding" role="tab">
                                    <i class="fas fa-palette mr-1"></i> Branding
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="modes-tab" data-toggle="tab" href="#modes" role="tab">
                                    <i class="fas fa-cogs mr-1"></i> Options
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="blocks-tab" data-toggle="tab" href="#blocks" role="tab">
                                    <i class="fas fa-th-list mr-1"></i> Layout Blocks
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold text-primary" id="ai-tab" data-toggle="tab" href="#ai" role="tab">
                                    <i class="fas fa-robot mr-1"></i> Gemini AI
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content border-0 p-0" id="customizerTabContent">

                            <!-- TAB 1: Branding -->
                            <div class="tab-pane fade show active" id="branding" role="tabpanel">
                                <form action="{{ route('booking_page.save') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group">
                                        <label class="font-weight-bold">Custom Page URL Slug <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-light text-muted">{{ url('/book') }}/</span>
                                            </div>
                                            <input type="text" name="slug" class="form-control font-weight-bold @error('slug') is-invalid @enderror" value="{{ old('slug', $setting->slug) }}" required>
                                        </div>
                                        <small class="form-text text-muted">Use alphanumeric characters and dashes only.</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Page Header Title <span class="text-danger">*</span></label>
                                        <input type="text" name="page_title" class="form-control font-weight-bold" value="{{ old('page_title', $setting->page_title) }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Tagline / Subheading</label>
                                        <input type="text" name="tagline" class="form-control" value="{{ old('tagline', $setting->tagline) }}" placeholder="e.g. Reliable appointments booked in seconds">
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label class="font-weight-bold">Primary Brand Color</label>
                                            <div class="input-group">
                                                <input type="color" name="primary_color" class="form-control form-control-color" value="{{ old('primary_color', $setting->primary_color ?: '#4b49ac') }}" style="max-width: 60px; height: 38px;">
                                                <input type="text" class="form-control" value="{{ old('primary_color', $setting->primary_color ?: '#4b49ac') }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="font-weight-bold">Secondary Accent Color</label>
                                            <div class="input-group">
                                                <input type="color" name="secondary_color" class="form-control form-control-color" value="{{ old('secondary_color', $setting->secondary_color ?: '#7978e9') }}" style="max-width: 60px; height: 38px;">
                                                <input type="text" class="form-control" value="{{ old('secondary_color', $setting->secondary_color ?: '#7978e9') }}" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Hero Banner Background Image</label>
                                        <input type="file" name="banner_image" class="form-control-file">
                                        @if($setting->banner_image)
                                            <div class="mt-2">
                                                <img src="{{ asset($setting->banner_image) }}" alt="Banner" width="160" class="img-thumbnail rounded">
                                            </div>
                                        @endif
                                    </div>

                                    <input type="hidden" name="allowed_service_modes" value="{{ $setting->allowed_service_modes }}">

                                    <button type="submit" class="btn btn-primary font-weight-bold px-4 mt-2">
                                        <i class="fas fa-save mr-1"></i> Save Branding Settings
                                    </button>
                                </form>
                            </div>

                            <!-- TAB 2: Options & Service Modes -->
                            <div class="tab-pane fade" id="modes" role="tabpanel">
                                <form action="{{ route('booking_page.save') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="slug" value="{{ $setting->slug }}">
                                    <input type="hidden" name="page_title" value="{{ $setting->page_title }}">
                                    <input type="hidden" name="primary_color" value="{{ $setting->primary_color }}">

                                    <div class="form-group mb-4">
                                        <label class="font-weight-bold">Allowed Service Delivery Modes</label>
                                        <select name="allowed_service_modes" class="form-control">
                                            <option value="both" {{ $setting->allowed_service_modes == 'both' ? 'selected' : '' }}>Both In-Facility & Home/On-Site Services</option>
                                            <option value="in_shop" {{ $setting->allowed_service_modes == 'in_shop' ? 'selected' : '' }}>In-Facility / Shop Only</option>
                                            <option value="home_service" {{ $setting->allowed_service_modes == 'home_service' ? 'selected' : '' }}>Home / Field On-Site Service Only</option>
                                        </select>
                                    </div>

                                    <div class="card bg-light border p-3 mb-3" style="border-radius: 10px;">
                                        <div class="custom-control custom-switch mb-3">
                                            <input type="checkbox" class="custom-control-input" id="enable_technician_picker" name="enable_technician_picker" value="1" {{ $setting->enable_technician_picker ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold" for="enable_technician_picker">
                                                Allow Customer to Select Technician / Specialist
                                            </label>
                                            <small class="d-block text-muted">When disabled, the system auto-assigns the best available staff.</small>
                                        </div>

                                        <div class="custom-control custom-switch mb-3">
                                            <input type="checkbox" class="custom-control-input" id="enable_customer_notes" name="enable_customer_notes" value="1" {{ $setting->enable_customer_notes ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold" for="enable_customer_notes">
                                                Enable Customer Problem Notes / Instructions Field
                                            </label>
                                            <small class="d-block text-muted">Lets customers describe special requests or problem symptoms.</small>
                                        </div>

                                        <div class="custom-control custom-switch mb-3">
                                            <input type="checkbox" class="custom-control-input" id="enable_ai_assistant" name="enable_ai_assistant" value="1" {{ $setting->enable_ai_assistant ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold" for="enable_ai_assistant">
                                                Enable Gemini AI Smart Booking Assistant
                                            </label>
                                            <small class="d-block text-muted">Renders a floating conversational AI assistant on the public page.</small>
                                        </div>

                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="is_published" name="is_published" value="1" {{ $setting->is_published ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold" for="is_published">
                                                Booking Page Published (Online)
                                            </label>
                                            <small class="d-block text-muted">When turned off, guests see a maintenance page.</small>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                        <i class="fas fa-save mr-1"></i> Save Options
                                    </button>
                                </form>
                            </div>

                            <!-- TAB 3: Block Layout Reordering -->
                            <div class="tab-pane fade" id="blocks" role="tabpanel">
                                <form action="{{ route('booking_page.save') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="slug" value="{{ $setting->slug }}">
                                    <input type="hidden" name="page_title" value="{{ $setting->page_title }}">
                                    <input type="hidden" name="primary_color" value="{{ $setting->primary_color }}">
                                    <input type="hidden" name="allowed_service_modes" value="{{ $setting->allowed_service_modes }}">

                                    <p class="text-muted small mb-3">
                                        <i class="fas fa-info-circle mr-1"></i> Below are the modular blocks of your booking page. You can customize their visibility:
                                    </p>

                                    <div class="list-group mb-4" id="sortableBlocks">
                                        @foreach($blocks as $idx => $block)
                                            <div class="list-group-item d-flex justify-content-between align-items-center mb-2 border rounded p-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-grip-vertical text-muted mr-3"></i>
                                                    <div>
                                                        <h6 class="mb-0 font-weight-bold">{{ $block['name'] }}</h6>
                                                        <small class="text-muted">Block ID: <code>{{ $block['id'] }}</code></small>
                                                    </div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="blk_{{ $block['id'] }}" name="layout_blocks[{{ $idx }}][visible]" value="1" {{ !empty($block['visible']) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="blk_{{ $block['id'] }}">Visible</label>
                                                    <input type="hidden" name="layout_blocks[{{ $idx }}][id]" value="{{ $block['id'] }}">
                                                    <input type="hidden" name="layout_blocks[{{ $idx }}][name]" value="{{ $block['name'] }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                        <i class="fas fa-save mr-1"></i> Save Layout Blocks
                                    </button>
                                </form>
                            </div>

                            <!-- TAB 4: Gemini AI Settings -->
                            <div class="tab-pane fade" id="ai" role="tabpanel">
                                <form action="{{ route('booking_page.ai_settings') }}" method="POST">
                                    @csrf

                                    <div class="form-group">
                                        <label class="font-weight-bold">AI Assistant Name</label>
                                        <input type="text" name="ai_assistant_name" class="form-control" value="{{ old('ai_assistant_name', $companySetting->ai_assistant_name ?? 'Smart Booking Assistant') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Custom Gemini API Key <span class="badge badge-light border text-muted">Optional</span></label>
                                        <input type="password" name="gemini_api_key" class="form-control" value="{{ old('gemini_api_key', $companySetting->gemini_api_key ?? '') }}" placeholder="AIzaSy... (Leave empty to use SaaS default key)">
                                        <small class="form-text text-muted">If empty, the SaaS platform global Gemini key is used automatically.</small>
                                    </div>

                                    <div class="form-group">
                                        <label class="font-weight-bold">Custom Business Instructions for AI</label>
                                        <textarea name="ai_instructions" class="form-control" rows="3" placeholder="e.g. Always emphasize our 100% money-back guarantee and inform customers that on-site home service calls require an adult present.">{{ old('ai_instructions', $companySetting->ai_instructions ?? '') }}</textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary font-weight-bold px-4">
                                        <i class="fas fa-save mr-1"></i> Save AI Settings
                                    </button>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- Live Preview Column -->
            <div class="col-lg-5 grid-margin stretch-card">
                <div class="card shadow-sm border-0" style="border-radius: 14px;">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="font-weight-bold"><i class="fas fa-mobile-alt mr-1 text-primary"></i> Mobile Live Preview</span>
                        <a href="{{ url('/book/' . $setting->slug) }}" target="_blank" class="small font-weight-bold text-primary">Open Full Page</a>
                    </div>
                    <div class="card-body p-2 d-flex justify-content-center bg-light" style="border-bottom-left-radius: 14px; border-bottom-right-radius: 14px;">
                        <div style="width: 100%; max-width: 380px; height: 650px; border: 8px solid #334155; border-radius: 36px; overflow: hidden; box-shadow: 0 20px 35px rgba(0,0,0,0.15); background: #ffffff;">
                            <iframe src="{{ url('/book/' . $setting->slug) }}" style="width: 100%; height: 100%; border: none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('btnCopyUrl').addEventListener('click', function () {
        const copyInput = document.getElementById('publicBookingUrl');
        copyInput.select();
        copyInput.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyInput.value);
        document.getElementById('copyBtnText').innerText = 'Copied!';
        setTimeout(() => { document.getElementById('copyBtnText').innerText = 'Copy Link'; }, 2000);
    });
</script>
@endsection
