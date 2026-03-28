<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Already Decided</title>
    <style>
        body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f3f4f6; }
        .card { background: #fff; border-radius: 8px; padding: 2rem 2.5rem; max-width: 420px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .icon { font-size: 2.5rem; margin-bottom: 1rem; }
        h1 { font-size: 1.25rem; color: #111; margin: 0 0 .5rem; }
        p { color: #6b7280; font-size: .95rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">ℹ️</div>
        <h1>Already Decided</h1>
        <p>You have already <strong>{{ $decision }}</strong> this requisition request. No further action is needed.</p>
    </div>
</body>
</html>