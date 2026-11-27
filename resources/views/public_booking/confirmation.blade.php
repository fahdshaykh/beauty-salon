<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed | {{ $company->company_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        :root {
            --primary: {{ $setting->primary_color ?: '#4b49ac' }};
            --dark: #1e293b;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 16px;
        }

        .confirmation-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 15px 35px rgba(0,0,0,0.06);
            max-width: 650px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #ecfdf5;
            color: #10b981;
            font-size: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            animation: popIn 0.4s ease-out;
        }

        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .booking-details-box {
            background: #f8fafc;
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            padding: 20px;
            text-align: left;
            margin: 24px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 14px;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #64748b;
        }
        .detail-value {
            font-weight: 700;
        }

        .btn-calendar {
            background: #ffffff;
            border: 1px solid var(--border);
            color: var(--dark);
            border-radius: 10px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-calendar:hover {
            border-color: var(--primary);
            color: var(--primary);
        }
    </style>
</head>
<body>

    <div class="confirmation-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h3 class="font-weight-bold mb-1">Appointment Confirmed!</h3>
        <p class="text-muted">Thank you, {{ optional($booking->customer)->first_name }}. Your booking reference is <strong>#{{ $booking->invoice }}</strong></p>

        <!-- Booking Details Box -->
        <div class="booking-details-box">
            <div class="detail-row">
                <span class="detail-label">Service(s)</span>
                <span class="detail-value text-primary">{{ $booking->services->pluck('name')->implode(', ') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date & Time</span>
                <span class="detail-value">
                    {{ \Carbon\Carbon::parse($booking->due_date)->format('l, d M Y') }} at {{ \Carbon\Carbon::parse($booking->due_time)->format('h:i A') }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Service Mode</span>
                <span class="detail-value">
                    @if($booking->is_home_service)
                        <span class="badge badge-warning text-dark"><i class="fas fa-home mr-1"></i> Home / On-Site</span>
                    @else
                        <span class="badge badge-info"><i class="fas fa-store mr-1"></i> Facility / Shop</span>
                    @endif
                </span>
            </div>
            @if($booking->is_home_service && $booking->booking_address)
            <div class="detail-row">
                <span class="detail-label">Service Address</span>
                <span class="detail-value">{{ $booking->booking_address }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Assigned Specialist</span>
                <span class="detail-value">{{ optional($booking->user)->name ?: 'Assigned Specialist' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount</span>
                <span class="detail-value text-success">${{ number_format($booking->total_price, 2) }}</span>
            </div>
        </div>

        <!-- Add to Calendar Links -->
        <?php
            $calTitle = urlencode($booking->services->pluck('name')->implode(', ') . ' - ' . $company->company_name);
            $calDetails = urlencode("Booking Invoice #{$booking->invoice}. Specialist: " . optional($booking->user)->name);
            $calLocation = urlencode($booking->is_home_service ? $booking->booking_address : ($company->company_address ?? ''));
            $startIso = \Carbon\Carbon::parse($booking->due_date . ' ' . $booking->due_time)->format('Ymd\THis');
            $endIso = \Carbon\Carbon::parse($booking->due_date . ' ' . $booking->due_time)->addMinutes($booking->service_time ?: 30)->format('Ymd\THis');
            $googleCalUrl = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$calTitle}&dates={$startIso}/{$endIso}&details={$calDetails}&location={$calLocation}";
        ?>

        <div class="mb-4">
            <span class="small font-weight-bold d-block text-muted mb-2">Save to your calendar:</span>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ $googleCalUrl }}" target="_blank" class="btn-calendar mr-2">
                    <i class="fab fa-google text-danger"></i> Google Calendar
                </a>
                <a href="javascript:void(0)" onclick="alert('Notification sent! You will receive SMS & Email reminders before your service.')" class="btn-calendar">
                    <i class="fas fa-bell text-warning"></i> Reminders Active
                </a>
            </div>
        </div>

        <a href="{{ url('/book/' . $setting->slug) }}" class="btn btn-outline-primary btn-block rounded-pill font-weight-bold py-2">
            Book Another Service
        </a>
    </div>

</body>
</html>
