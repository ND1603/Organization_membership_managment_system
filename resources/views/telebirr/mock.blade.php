<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telebirr Payment — Mock</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        .logo {
            background: #F97316;
            color: white;
            font-size: 24px;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 24px;
        }
        .mock-badge {
            background: #FEF3C7;
            color: #92400E;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 20px;
            display: inline-block;
        }
        h2 { font-size: 20px; margin-bottom: 8px; color: #1a1a1a; }
        .amount {
            font-size: 36px;
            font-weight: bold;
            color: #F97316;
            margin: 16px 0;
        }
        .amount span { font-size: 18px; }
        label {
            display: block;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 20px;
        }
        input:focus { outline: none; border-color: #F97316; }
        .btn {
            width: 100%;
            padding: 14px;
            background: #F97316;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover { background: #EA6C0A; }
        .ref {
            font-size: 12px;
            color: #999;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">telebirr</div>
        <div class="mock-badge">🧪 Mock / Test Mode</div>

        <h2>Complete Payment</h2>
        <p style="color:#666; font-size:14px;">Enter your Telebirr PIN to confirm</p>

        <div class="amount">
            <span>ETB</span> {{ number_format($amount, 2) }}
        </div>

        <form method="POST" action="{{ route('telebirr.mock.pay') }}">
            @csrf
            <input type="hidden" name="out_trade_no" value="{{ $outTradeNo }}">

            <label>Telebirr Phone Number</label>
            <input type="text" name="phone" placeholder="+251 9XX XXX XXX" value="+251900000000">

            <label>PIN (any 4 digits in mock mode)</label>
            <input type="password" name="pin" placeholder="••••" maxlength="4">

            <button type="submit" class="btn">Confirm Payment</button>
        </form>

        <p class="ref">Ref: {{ $outTradeNo }}</p>
    </div>
</body>
</html>