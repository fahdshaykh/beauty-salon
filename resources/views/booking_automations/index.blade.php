@extends('layouts.admin')

@section('title', 'Booking Automations Hub | ' . Session::get('site_name'))

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="page-title font-weight-bold">
                    <i class="fas fa-bolt text-warning mr-2"></i> Booking Automations Hub
                </h3>
                <p class="text-muted small mb-0">Automate your service booking pipeline: confirmations, reminders, status updates, and customer win-backs</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <!-- Merge Tags Cheat Sheet -->
        <div class="card mb-4 border-0 shadow-sm" style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-tags text-primary mr-2"></i>
                    <strong class="small text-uppercase text-muted">Available Dynamic Placeholder Tags</strong>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{customer_name}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{service_name}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{due_date}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{due_time}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{technician_name}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{booking_address}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{invoice_no}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{total_price}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{tracking_link}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{review_link}</code></span>
                    <span class="badge badge-light border text-dark py-1 px-2"><code>{company_name}</code></span>
                </div>
            </div>
        </div>

        <!-- Automation Cards -->
        <div class="row">
            @foreach($availableEvents as $eventKey => $eventInfo)
                @php
                    $auto = $automations->get($eventKey);
                    $isEnabled = $auto ? $auto->is_enabled : true;
                    $channel = $auto ? $auto->channel : 'both';
                    $smsTemplate = $auto ? $auto->sms_template : '';
                    $emailSubject = $auto ? $auto->email_subject : '';
                    $emailTemplate = $auto ? $auto->email_template : '';
                @endphp
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border h-100" style="border-radius: 14px;">
                        <form action="{{ route('booking_automations.update', $eventKey) }}" method="POST">
                            @csrf

                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3" style="font-size: 20px;">
                                        <i class="{{ $eventInfo['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold mb-0 text-dark">{{ $eventInfo['name'] }}</h6>
                                        <small class="text-muted">{{ $eventInfo['description'] }}</small>
                                    </div>
                                </div>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="switch_{{ $eventKey }}" name="is_enabled" value="1" {{ $isEnabled ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="switch_{{ $eventKey }}"></label>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="form-group">
                                    <label class="small font-weight-bold">Dispatch Channel</label>
                                    <select name="channel" class="form-control form-control-sm">
                                        <option value="both" {{ $channel == 'both' ? 'selected' : '' }}>SMS & Email (Both)</option>
                                        <option value="sms" {{ $channel == 'sms' ? 'selected' : '' }}>SMS Only</option>
                                        <option value="email" {{ $channel == 'email' ? 'selected' : '' }}>Email Only</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="small font-weight-bold">SMS Message Template</label>
                                    <textarea name="sms_template" class="form-control form-control-sm" rows="2" placeholder="SMS text template...">{{ $smsTemplate }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label class="small font-weight-bold">Email Subject</label>
                                    <input type="text" name="email_subject" class="form-control form-control-sm" value="{{ $emailSubject }}" placeholder="Email subject line...">
                                </div>

                                <div class="form-group mb-0">
                                    <label class="small font-weight-bold">Email Body Template</label>
                                    <textarea name="email_template" class="form-control form-control-sm" rows="3" placeholder="Email body template...">{{ $emailTemplate }}</textarea>
                                </div>
                            </div>

                            <div class="card-footer bg-light border-top py-2 text-right">
                                <button type="submit" class="btn btn-sm btn-primary font-weight-bold px-3">
                                    <i class="fas fa-save mr-1"></i> Save Rule
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
