<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decision Recorded</title>
    <style>
        body { font-family: sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f3f4f6; }
        .card { background: #fff; border-radius: 8px; padding: 2rem 2.5rem; max-width: 420px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .icon { font-size: 2.5rem; margin-bottom: 1rem; }
        h1 { font-size: 1.25rem; color: #111; margin: 0 0 .5rem; }
        p { color: #6b7280; font-size: .95rem; }
        .badge { display: inline-block; margin-top: .5rem; padding: .25rem .75rem; border-radius: 999px; font-size: .875rem; font-weight: 600; }
        .approved { background: #dcfce7; color: #15803d; }
        .rejected { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">{{ $action === 'approved' ? '✅' : '❌' }}</div>
        <h1>Decision Recorded</h1>
        <p>You have successfully <span class="badge {{ $action }}">{{ $action }}</span> requisition <strong>{{ $reference }}</strong>.</p>
        <p style="margin-top: 1rem;">You may now close this window.</p>
    </div>
</body>
</html>