@extends('admin.layouts.admin')
@section('title', 'Company Limits & Overrides | Admin')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <i class="fas fa-shield-alt"></i>
                Limits & Overrides – {{ $company->company_name }}
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Companies</a></li>
                    <li class="breadcrumb-item active">Limits</li>
                </ol>
            </nav>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title mb-1">Subscription Plan</h4>
                                <p class="text-muted">Current: <strong>{{ $limits->plan?->name ?? 'No Plan' }}</strong></p>
                            </div>
                            <a href="{{ route('admin.companies.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>

                        <form action="{{ route('admin.limits.update', $company) }}" method="POST">
                            @csrf
                            @method('POST')

                            <!-- Plan Selector -->
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Change Subscription Plan</label>
                                <div class="col-md-6">
                                    <select name="subscription_plan_id" class="form-control">
                                        <option value="">-- Use Current Plan --</option>
                                        @foreach(\App\Models\SubscriptionPlan::all() as $plan)
                                            <option value="{{ $plan->id }}"
                                                {{ $limits->subscription_plan_id == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }} – ${{ $plan->price_yearly }}/year
                                                {{ $plan->is_unlimited ? ' (Unlimited)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <hr>

                            <h5 class="mt-4 mb-4 text-primary">
                                <i class="fas fa-tachometer-alt"></i> Monthly Limits & Usage
                            </h5>

                            <div class="row">
                                @php
                                    $resources = [
                                        'employees'     => ['label' => 'Employees',       'icon' => 'fa-users'],
                                        'customers'     => ['label' => 'Customers',       'icon' => 'fa-user-friends'],
                                        'bookings'      => ['label' => 'Bookings',        'icon' => 'fa-calendar-check'],
                                        'quotations'    => ['label' => 'Quotations',      'icon' => 'fa-file-invoice'],
                                        'emails'        => ['label' => 'Emails Sent',     'icon' => 'fa-envelope'],
                                        'invoices'      => ['label' => 'Invoices',        'icon' => 'fa-file-alt'],
                                    ];
                                @endphp

                                @foreach($resources as $key => $item)
                                    @php
                                        $used = $limits->{"{$key}_used"} ?? 0;
                                        $override = $limits->{"override_{$key}"};
                                        $planLimit = $limits->plan?->{"max_{$key}"} ?? null;
                                        $finalLimit = $override ?? $planLimit;
                                        $isUnlimited = $limits->plan?->is_unlimited || is_null($finalLimit);
                                        $percentage = $isUnlimited ? 0 : ($finalLimit > 0 ? min(100, ($used / $finalLimit) * 100) : 100);
                                        $progressColor = $percentage >= 90 ? 'danger' : ($percentage >= 70 ? 'warning' : 'success');
                                    @endphp

                                    <div class="col-md-6 mb-4">
                                        <div class="card border-left-primary shadow h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h6 class="mb-0 font-weight-bold text-gray-800">
                                                        <i class="fas {{ $item['icon'] }} mr-2"></i>
                                                        {{ $item['label'] }}
                                                    </h6>
                                                    <span class="badge badge-{{ $progressColor }}">
                                                        {{ $used }} / {{ $isUnlimited ? 'Unlimited' : ($override ? "$override (Override)" : $finalLimit) }}
                                                    </span>
                                                </div>

                                                <!-- Progress Bar -->
                                                @if(!$isUnlimited)
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $progressColor }}" role="progressbar"
                                                         style="width: {{ $percentage }}%"
                                                         aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ number_format($percentage, 0) }}%
                                                    </div>
                                                </div>
                                                @else
                                                <div class="text-center text-success font-weight-bold">Unlimited</div>
                                                @endif

                                                <!-- Override Input -->
                                                <div class="mt-3">
                                                    <label class="small text-muted">Override Limit (leave empty = use plan)</label>
                                                    <input type="number" name="override_{{ $key }}"
                                                           class="form-control form-control-sm @if($override !== null) border-warning @endif"
                                                           value="{{ $override }}"
                                                           placeholder="e.g. 999 for unlimited, 0 to block">
                                                    <small class="text-muted">
                                                        Plan limit: {{ $planLimit ? $planLimit : 'Unlimited' }}
                                                        @if($override !== null)
                                                            → <strong class="text-danger">Override Active</strong>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-save"></i> Save All Changes
                                </button>
                                <a href="{{ route('admin.companies.index') }}" class="btn btn-light btn-lg">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection