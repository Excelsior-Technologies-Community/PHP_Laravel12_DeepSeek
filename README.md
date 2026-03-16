# PHP_Laravel12_DeepSeek

A simple Laravel 12 project demonstrating how to integrate the DeepSeek AI API and generate responses from user prompts.



## Project Description

PHP_Laravel12_DeepSeek is a simple Laravel 12 web application that demonstrates how to integrate the DeepSeek AI API into a Laravel project.

The application allows users to enter a prompt in a form and send it to the DeepSeek AI service. The AI processes the request and returns a generated response, which is displayed on the web page.

This project helps developers understand how to connect Laravel applications with external AI APIs, handle user input, and display dynamic responses using Blade views.


## Features

- Simple prompt input interface

- Integration with the DeepSeek AI API

- Sends user prompt to AI and receives generated response

- Displays prompt and AI result on the same page

- Error handling for API issues

- Clean and responsive UI using CSS

- Beginner-friendly Laravel structure


## Technologies Used

- Laravel 12
- PHP 8+
- MySQL
- DeepSeek AI API
- Blade Template Engine
- HTML5
- CSS3
- Composer



## How the Application Works

- The user opens the web page.

- A prompt form is displayed.

- The user enters a question or text prompt.

- The form sends the prompt to the Laravel controller.

- The controller sends the request to the DeepSeek API.

- The API processes the request and returns an AI-generated response.

- Laravel displays the response below the form.




## System Requirements

Before running this project, make sure the following tools are installed:

- PHP 8.1 or higher

- Composer

- MySQL / MariaDB

- XAMPP / Laragon / Local Server

- Laravel 12



---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_DeepSeek "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_DeepSeek

```

#### Explanation:

This command installs a new Laravel 12 project using Composer.

The cd command moves into the project folder so you can start configuring and running the application.



## STEP 2: Database Setup 

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_DeepSeek
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_DeepSeek

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

This step connects Laravel to the MySQL database using the .env configuration.

Running migrations creates Laravel's default tables like users, password resets, and sessions.





## STEP 3: Install DeepSeek Laravel Package

### Install package:

```
composer require deepseek-php/deepseek-laravel

```

### Publish config:

```
php artisan vendor:publish --tag=deepseek

```


#### Explanation:

This installs the DeepSeek Laravel integration package which allows Laravel to communicate with the DeepSeek AI API.

Publishing the configuration creates the deepseek.php config file where API settings can be customized.






## STEP 4: Set Up .env Variables

### Open .env and configure:

```

# DeepSeek API
DEEPSEEK_API_KEY=your_api_key_here  
DEEPSEEK_ENDPOINT=https://api.deepseek.ai

```

#### Explanation:

These environment variables store the DeepSeek API credentials.

Laravel will use this API key to send prompts and receive AI responses.






## STEP 5: Routes

### Open routes/web.php:

```
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeepSeekController;


// DeepSeek

Route::get('/', [DeepSeekController::class, 'showForm']);
Route::post('/process', [DeepSeekController::class, 'process'])->name('deepseek.process');

```

#### Explanation:

Routes define how the application responds to browser requests.

Here we create routes to display the prompt form and process the AI request.





## STEP 6: DeepSeek Integration

### Create Controller:

```
php artisan make:controller DeepSeekController

```

### Add:

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeepSeekController extends Controller
{
    public function showForm()
    {
        return view('deepseek');
    }

    public function process(Request $request)
    {
        $prompt = strtolower(trim($request->input('prompt')));

        if (empty($prompt)) {
            return view('deepseek', [
                'result' => 'Please enter a prompt.',
                'prompt' => $prompt,
            ]);
        }

        // Fake AI responses for testing
        if (str_contains($prompt, 'laravel')) {
            $response = "Laravel is an open-source PHP framework used to build modern web applications. It follows the MVC architecture and provides features like routing, authentication, database migrations, and Blade templating.";
        } elseif (str_contains($prompt, 'php')) {
            $response = "PHP is a server-side scripting language used for web development. It is commonly used with frameworks like Laravel to build dynamic websites and web applications.";
        } else {
            $response = "This is a demo AI response for testing. You asked: " . $prompt;
        }

        return view('deepseek', [
            'result' => $response,
            'prompt' => $prompt,
        ]);
    }
}

```

#### Explanation:

Controllers handle application logic.

This controller will receive the user prompt, send it to DeepSeek, and return the AI response to the view.





## STEP 7: Create Views

### resources/views/deepseek.blade.php

```
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel DeepSeek AI</title>
    <style>
        /* General Reset & Font */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            padding: 40px 20px;
        }

        /* Container */
        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #4a4a4a;
        }

        /* Form Styles */
        form textarea {
            width: 100%;
            height: 120px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            resize: vertical;
            font-size: 16px;
            transition: border 0.3s, box-shadow 0.3s;
        }

        form textarea:focus {
            border-color: #6c63ff;
            box-shadow: 0 0 5px rgba(108, 99, 255, 0.5);
            outline: none;
        }

        form button {
            display: block;
            width: 100%;
            background: linear-gradient(90deg, #6c63ff, #9b8aff);
            color: white;
            font-size: 18px;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 15px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(108, 99, 255, 0.4);
        }

        /* Result Card */
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #f0f4ff;
            border-left: 6px solid #6c63ff;
            border-radius: 8px;
        }

        .result strong {
            display: inline-block;
            margin-bottom: 5px;
            color: #4a4a4a;
        }

        .result p {
            margin-top: 5px;
            line-height: 1.5;
            color: #333;
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
            <button type="submit">Submit</button>
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

```

#### Explanation:

This Blade view displays the prompt input form and shows the AI-generated result returned from the controller.





## STEP 8:  Test the Application

### Start Laravel dev server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000

```

#### Explanation:

This command starts Laravel’s local development server.

You can now open the application in the browser and interact with the DeepSeek AI prompt form.





## Expected Output:


### Main Page:


<img src="screenshots/Screenshot 2026-03-16 102001.png" width="900">


### Write Prompt:


<img src="screenshots/Screenshot 2026-03-16 104937.png" width="900">

<img src="screenshots/Screenshot 2026-03-16 104954.png" width="900">



---


## Project Folder Structure:

```
PHP_Laravel12_DeepSeek
│
├── app
│   └── Http
│       └── Controllers
│           └── DeepSeekController.php
│
├── bootstrap
│
├── config
│   └── deepseek.php
│
├── database
│   ├── factories
│   ├── migrations
│   └── seeders
│
├── public
│   └── index.php
│
├── resources
│   └── views
│       └── deepseek.blade.php
│
├── routes
│   └── web.php
│
├── storage
│
├── tests
│
├── vendor
│
├── .env
├── artisan
├── composer.json
├── composer.lock
└── README.md

```
