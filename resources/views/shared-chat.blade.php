<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Chat - DeepSeek AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0d1117; --surface:#161b22; --border:#30363d; --text:#e6edf3; --muted:#7d8590; --accent:#7c3aed; }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',sans-serif; }
        body { background:var(--bg); color:var(--text); min-height:100vh; padding:40px 20px; }
        .container { max-width:720px; margin:auto; }
        .header { text-align:center; margin-bottom:32px; }
        .header h1 { font-size:24px; background:linear-gradient(135deg,#6366f1,#7c3aed); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .card { background:var(--surface); border:1px solid var(--border); border-radius:16px; padding:24px; margin-bottom:16px; }
        .label { font-size:11px; color:var(--muted); margin-bottom:8px; text-transform:uppercase; letter-spacing:.05em; }
        .user-card { border-left:3px solid #6366f1; }
        .ai-card   { border-left:3px solid var(--accent); }
        pre { white-space:pre-wrap; font-family:'Inter',sans-serif; font-size:14px; line-height:1.7; }
        .footer { text-align:center; font-size:12px; color:var(--muted); margin-top:24px; }
        .back-btn { display:inline-block; margin-top:16px; padding:9px 20px; background:linear-gradient(135deg,#6366f1,#7c3aed); border-radius:10px; color:white; text-decoration:none; font-size:13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤖 DeepSeek AI - Shared Conversation</h1>
            <p style="color:var(--muted); font-size:13px; margin-top:8px;">{{ $chat->created_at->format('F j, Y \a\t g:i A') }}</p>
        </div>

        <div class="card user-card">
            <div class="label">👤 User asked</div>
            <pre>{{ $chat->prompt }}</pre>
        </div>

        <div class="card ai-card">
            <div class="label">🤖 DeepSeek AI responded</div>
            <pre>{{ $chat->response }}</pre>
        </div>

        <div class="footer">
            <p>Model: {{ $chat->model_used }} • Tokens: {{ number_format($chat->tokens_used ?? 0) }}</p>
            <a href="{{ route('deepseek.form') }}" class="back-btn">Try DeepSeek AI →</a>
        </div>
    </div>
</body>
</html>