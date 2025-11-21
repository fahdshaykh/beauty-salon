<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
        }
        .content {
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .info-table th {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        .info-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Booking Confirmation</h1>
            <p>{{ $company_title ?? 'Beauty Salon' }}</p>
        </div>

        <div class="content">
            <p>Dear <strong>{{ $customer_name }}</strong>,</p>
            <p>Thank you for booking with us! Here are your booking details:</p>

            <table class="info-table">
                <tr>
                    <th>Invoice Number</th>
                    <td>{{ $invoice_number }}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>{{ $due_date }}</td>
                </tr>
                <tr>
                    <th>Time</th>
                    <td>{{ $due_time }}</td>
                </tr>
                <tr>
                    <th>Services</th>
                    <td>{{ $services }}</td>
                </tr>
                <tr>
                    <th>Total Price</th>
                    <td>£{{ $total_price }}</td>
                </tr>
                <tr>
                    <th>VAT</th>
                    <td>£{{ $vat }}</td>
                </tr>
                @if($comment)
                <tr>
                    <th>Notes</th>
                    <td>{{ $comment }}</td>
                </tr>
                @endif
            </table>

            <p>We look forward to seeing you on the scheduled date and time!</p>
            <p>If you have any questions or need to reschedule, please contact us.</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $company_title ?? 'Beauty Salon' }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
