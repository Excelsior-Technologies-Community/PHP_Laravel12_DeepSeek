<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel DeepSeek AI - Dark Theme</title>
    <style>
        /* Reset & Font */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #121212;
            color: #fff;
            padding: 40px 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #1e1e1e;
            padding: 30px 35px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ff6f61;
        }

        /* Form Styles */
        form textarea {
            width: 100%;
            height: 120px;
            padding: 15px;
            border: none;
            border-radius: 12px;
            resize: vertical;
            font-size: 16px;
            background: #2c2c2c;
            color: #fff;
            transition: box-shadow 0.3s;
        }

        form textarea:focus {
            outline: none;
            box-shadow: 0 0 10px #ff6f61;
        }

        form button {
            display: block;
            width: 100%;
            background: #ff6f61;
            color: white;
            font-size: 18px;
            padding: 12px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(255, 111, 97, 0.4);
        }

        /* Result Card */
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #292929;
            border-left: 6px solid #ff6f61;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .result strong {
            display: inline-block;
            margin-bottom: 5px;
            color: #ff6f61;
        }

        .result p {
            margin-top: 5px;
            line-height: 1.6;
            color: #e0e0e0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            form textarea {
                height: 100px;
            }

            form button {
                font-size: 16px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Laravel DeepSeek AI</h2>

        <form action="{{ route('deepseek.process') }}" method="POST">
            @csrf
            <textarea name="prompt"
                placeholder="Type your prompt here">{{ old('prompt') ?? ($prompt ?? '') }}</textarea>
            <button type="submit">Send</button>
        </form>

        @if(isset($result))
            <div class="result">
                <strong>Prompt:</strong>
                <p>{{ $prompt }}</p>
                <strong>Result:</strong>
                <p>{{ $result }}</p>
            </div>
        @endif
    </div>

</body>

</html>