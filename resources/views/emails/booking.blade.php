<!DOCTYPE html>
<html>
<head>
    <title>Booking Reminder</title>
</head>
<body>
    <h1>Dear {{ $booking->customer->first_name }},</h1>
    <p>This is a reminder for your upcoming booking:</p>
    <ul>
        <li>Booking ID: {{ $booking->id }}</li>
        <li>Date: {{ $booking->due_date }}</li>
        <li>Time: {{ $booking->due_time }}</li>
    </ul>
    <p>Your booking is scheduled in 30 minutes. Please make necessary arrangements.</p>
</body>
</html>
