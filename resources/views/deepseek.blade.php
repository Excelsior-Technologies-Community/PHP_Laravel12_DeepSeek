<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeepSeek AI Assistant</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins',sans-serif;
        }

        body{
            background:#0f172a;
            color:white;
            min-height:100vh;
            padding:40px 20px;
            transition:0.3s;
        }

        body.light{
            background:#f3f4f6;
            color:#111827;
        }

        .container{
            max-width:900px;
            margin:auto;
        }

        .card{
            background:#1e293b;
            padding:30px;
            border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.3);
            margin-bottom:30px;
            transition:0.3s;
        }

        body.light .card{
            background:white;
            box-shadow:0 10px 30px rgba(0,0,0,0.08);
        }

        h1{
            text-align:center;
            margin-bottom:25px;
            font-size:36px;
        }

        h2{
            margin-bottom:20px;
        }

        body.light h1,
        body.light h2,
        body.light h3,
        body.light p,
        body.light strong{
            color:#111827;
        }

        textarea{
            width:100%;
            height:140px;
            padding:20px;
            border:none;
            border-radius:15px;
            resize:none;
            font-size:16px;
            outline:none;
            background:#0f172a;
            color:white;
            border:2px solid transparent;
            transition:0.3s;
        }

        textarea:focus{
            border-color:#8b5cf6;
            box-shadow:0 0 10px rgba(139,92,246,0.4);
        }

        textarea::placeholder{
            color:#cbd5e1;
        }

        body.light textarea{
            background:white;
            color:#111827;
            border:2px solid #d1d5db;
        }

        body.light textarea::placeholder{
            color:#6b7280;
        }

        button{
            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            color:white;
            border:none;
            padding:14px 30px;
            border-radius:12px;
            font-size:16px;
            cursor:pointer;
            margin-top:15px;
            transition:0.3s;
        }

        button:hover{
            transform:translateY(-2px);
            box-shadow:0 6px 20px rgba(99,102,241,0.4);
        }

        .response{
            margin-top:25px;
            background:#334155;
            padding:20px;
            border-radius:15px;
            line-height:1.8;
            transition:0.3s;
        }

        body.light .response{
            background:#f3f4f6;
            border:1px solid #d1d5db;
        }

        .history{
            margin-top:15px;
            padding:20px;
            border-radius:12px;
            background:#334155;
            line-height:1.7;
            transition:0.3s;
        }

        body.light .history{
            background:#f9fafb;
            border:1px solid #d1d5db;
        }

        .toggle{
            float:right;
            margin-bottom:15px;
        }

        .copy-btn{
            background:#10b981;
            margin-top:15px;
        }

        .copy-btn:hover{
            box-shadow:0 6px 20px rgba(16,185,129,0.4);
        }

        .loader{
            display:none;
            text-align:center;
            margin-top:20px;
            font-size:18px;
            animation:pulse 1s infinite;
        }

        @keyframes pulse{
            0%{
                opacity:0.5;
            }
            50%{
                opacity:1;
            }
            100%{
                opacity:0.5;
            }
        }

        @media(max-width:768px){

            body{
                padding:20px 15px;
            }

            .card{
                padding:20px;
            }

            h1{
                font-size:28px;
            }

            textarea{
                height:120px;
            }

            button{
                width:100%;
            }

            .toggle{
                width:auto;
                float:none;
                margin-bottom:20px;
            }
        }

    </style>
</head>

<body>

<div class="container">

    <button class="toggle" onclick="toggleMode()">
        Toggle Mode
    </button>

    <div class="card">

        <h1>DeepSeek AI Assistant</h1>

        <form method="POST"
              action="{{ route('deepseek.process') }}"
              onsubmit="showLoader()">

            @csrf

            <textarea
                name="prompt"
                placeholder="Ask anything from AI..."
            >{{ old('prompt') ?? ($prompt ?? '') }}</textarea>

            <button type="submit">
                Generate AI Response
            </button>

        </form>

        <div class="loader" id="loader">
            <p>AI is generating response...</p>
        </div>

        @if(isset($result))

            <div class="response">

                <h3>Your Prompt</h3>

                <p>{{ $prompt }}</p>

                <br>

                <h3>AI Response</h3>

                <p id="aiText">{{ $result }}</p>

                <button class="copy-btn"
                        onclick="copyText()">

                    Copy Response

                </button>

            </div>

        @endif

    </div>

    <div class="card">

        <h2>Recent Chat History</h2>

        @forelse($histories as $history)

            <div class="history">

                <strong>Prompt:</strong>

                <p>{{ $history->prompt }}</p>

                <br>

                <strong>Response:</strong>

                <p>{{ $history->response }}</p>

            </div>

        @empty

            <p>No chat history found.</p>

        @endforelse

    </div>

</div>

<script>

    function copyText(){

        let text = document.getElementById('aiText').innerText;

        navigator.clipboard.writeText(text);

        alert('Copied Successfully');

    }

    function toggleMode(){

        document.body.classList.toggle('light');

    }

    function showLoader(){

        document.getElementById('loader').style.display = 'block';

    }

</script>

</body>

</html>