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