<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fayda — Mock Login</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a3c5e 0%, #2d6a9f 100%);
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
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            text-align: center;
        }
        .logo {
            background: #1a3c5e;
            color: white;
            font-size: 20px;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 10px;
            display: inline-block;
            margin-bottom: 8px;
        }
        .flag {
            font-size: 24px;
            margin-bottom: 20px;
            display: block;
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
        h2 { font-size: 20px; margin-bottom: 6px; color: #1a1a1a; }
        p { color: #666; font-size: 14px; margin-bottom: 24px; }
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
            margin-bottom: 16px;
        }
        input:focus { outline: none; border-color: #1a3c5e; }
        .btn {
            width: 100%;
            padding: 14px;
            background: #1a3c5e;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn:hover { background: #15324f; }
        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">🇪🇹 Fayda</div>
        <span class="flag"></span>
        <div class="mock-badge">🧪 Mock / Test Mode</div>

        <h2>Sign in with Fayda</h2>
        <p>Enter your Fayda credentials to verify your national identity.</p>

        <form method="POST" action="{{ route('fayda.mock.login') }}">
            @csrf
            <input type="hidden" name="state" value="{{ $state }}">

            <label>Fayda ID / FIN</label>
            <input type="text" name="fin" placeholder="e.g. ETH12345678" value="ETH12345678">

            <label>Password (any value in mock mode)</label>
            <input type="password" name="password" placeholder="••••••••">

            <button type="submit" class="btn">Verify Identity</button>
        </form>

        <p class="footer">
            This is a mock page for development.<br>
            In production this page is hosted by Fayda.
        </p>
    </div>
</body>
</html>