<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            color: #0066cc;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }
        .meta div { width: 48%; }
        .meta strong { display: block; margin-bottom: 4px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #0066cc;
            color: white;
            padding: 10px;
            text-align: left;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
        }
        .badge-approved {
            background: #28a745;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
        }
        .footer {
            margin-top: 50px;
            font-size: 11px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>PAYMENT INVOICE</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="meta">
        <div>
            <strong>Invoice Details</strong>
            Invoice #: {{ $invoiceNumber }}<br>
            Date: {{ now()->format('d M Y') }}<br>
            Status: <span class="badge-approved">APPROVED</span>
        </div>
        <div>
            <strong>Member Details</strong>
            Name: {{ $verification->user->name }}<br>
            Email: {{ $verification->user->email }}<br>
            Organization: {{ $verification->user->organization_name ?? 'N/A' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Transaction ID</th>
                <th>Date</th>
                <th>Amount (ETB)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Membership Payment</td>
                <td>{{ $verification->extracted_transaction_id ?? 'N/A' }}</td>
                <td>{{ $verification->extracted_date ? \Carbon\Carbon::parse($verification->extracted_date)->format('d M Y') : 'N/A' }}</td>
                <td>{{ number_format($verification->extracted_amount ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        Total: ETB {{ number_format($verification->extracted_amount ?? 0, 2) }}
    </div>

    @if($verification->admin_note)
    <p style="margin-top: 20px;"><strong>Admin Note:</strong> {{ $verification->admin_note }}</p>
    @endif

    <div class="footer">
        Verified by: {{ $verification->verifiedBy->name ?? 'Admin' }}
        &nbsp;|&nbsp;
        Verified at: {{ $verification->verified_at ? $verification->verified_at->format('d M Y H:i') : now()->format('d M Y H:i') }}
        <br><br>
        This is a computer-generated invoice. No signature required.
    </div>

</body>
</html>