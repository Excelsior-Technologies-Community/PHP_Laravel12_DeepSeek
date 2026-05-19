<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Chat - DeepSeek AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #0f172a;
            color: white;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .card {
            background: #1e293b;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .chat-bubble {
            margin-bottom: 25px;
            padding: 15px 20px;
            border-radius: 15px;
        }
        .user-bubble {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            margin-left: 20%;
        }
        .ai-bubble {
            background: #334155;
            margin-right: 20%;
        }
        .label {
            font-size: 12px;
            opacity: 0.7;
            margin-bottom: 8px;
        }
        @media (max-width: 768px) {
            .user-bubble, .ai-bubble {
                margin-left: 10%;
                margin-right: 10%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 style="margin-bottom: 30px; text-align: center;">
                <i class="fas fa-robot"></i> Shared Conversation
            </h1>
            
            <div class="chat-bubble user-bubble">
                <div class="label">You asked:</div>
                <p>{{ $chat->prompt }}</p>
            </div>
            
            <div class="chat-bubble ai-bubble">
                <div class="label">DeepSeek AI responded:</div>
                <p style="white-space: pre-wrap;">{{ $chat->response }}</p>
            </div>
            
            <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #334155;">
                <p style="font-size: 12px; opacity: 0.7;">
                    Shared on {{ $chat->created_at->format('F j, Y g:i A') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>