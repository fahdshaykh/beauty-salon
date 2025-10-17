<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .sub-header {
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            padding: 6px;
            vertical-align: top;
        }
        .pay-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .pay-table th,
        .pay-table td {
            border: 1px solid #000;
            padding: 10px;
            text-align: right;
        }
        .pay-table th {
            background-color: #f4f4f4;
            text-align: center;
        }
        .total-row {
            font-weight: bold;
        }
        .center {
            text-align: center;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signatures td {
            padding-top: 40px;
            text-align: center;
        }
        .footer-note {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            font-style: italic;
        }
    </style>
</head>
<body>

    @php
        $transport_deduction = ($user->is_transport === 'yes' && $user->transport_allowance) ? $user->transport_allowance : 0;
        $tax = 0.10 * ($base_salary + $commission + $overtime_payment);
        $leave_deduction = $deductions - $tax - $transport_deduction;
    @endphp

    <div class="header">
        <h2>Monthly Payslip</h2>
    </div>

    <div class="sub-header">
        <strong>Pay Period:</strong> {{ \Carbon\Carbon::parse($pay_period_start)->format('F 1, Y') }} - {{ \Carbon\Carbon::parse($pay_period_end)->format('F d, Y') }}
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Employee Name:</strong> {{ $user->name }}</td>
            <td><strong>Designation:</strong> {{ $user->designation ?? 'N/A' }}</td>
        </tr>
    </table>

    <table class="pay-table">
        <tr>
            <th colspan="2">Earnings</th>
            <th colspan="2">Deductions</th>
        </tr>
        <tr>
            <td>Base Salary</td>
            <td>{{ number_format($base_salary, 2) }}</td>
            <td>Tax (10%)</td>
            <td>{{ number_format($tax, 2) }}</td>
        </tr>
        <tr>
            <td>Commission</td>
            <td>{{ number_format($commission, 2) }}</td>
            <td>Leave Deduction</td>
            <td>{{ number_format($leave_deduction, 2) }}</td>
        </tr>
        <tr>
            <td>Overtime</td>
            <td>{{ number_format($overtime_payment, 2) }}</td>
            <td>Transport Deduction</td>
            <td>{{ number_format($transport_deduction, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Total Earnings</td>
            <td>{{ number_format($base_salary + $commission + $overtime_payment, 2) }}</td>
            <td>Total Deductions</td>
            <td>{{ number_format($deductions, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td colspan="3" class="center">Net Pay</td>
            <td>{{ number_format($net_pay, 2) }}</td>
        </tr>
    </table>

    <div class="center">
        <strong>In Words:</strong><br>
        <em>{{ ucwords(\NumberFormatter::create('en', \NumberFormatter::SPELLOUT)->format($net_pay)) }} only.</em>
    </div>

    <table class="signatures">
        <tr>
            <td>Employer Signature</td>
            <td>Employee Signature</td>
        </tr>
        <tr>
            <td>_______________________</td>
            <td>_______________________</td>
        </tr>
    </table>

    <div class="footer-note">
        This is a system-generated payslip and does not require signature or seal.
    </div>

</body>
</html>
