<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Booking Confirmation</h1>
    <p>Dear {{ $bookingDetails['client_name'] }},</p>
    <p>Thank you for your booking!</p>
    <p>Here are your booking details:</p>
    <ul>
        <li>Service: {{ $bookingDetails['service'] }}</li>
        <li>Date: {{ $bookingDetails['date'] }}</li>
        <li>Time: {{ $bookingDetails['time'] }}</li>
        <li>Location: {{ $bookingDetails['location'] }}</li>
    </ul>
    <p>We look forward to seeing you!</p>
    <p>Best regards,<br>Your Company Name</p>
</body>
</html>
