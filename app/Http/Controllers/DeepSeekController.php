<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatHistory;

class DeepSeekController extends Controller
{
    public function showForm()
    {
        $histories = ChatHistory::latest()->take(10)->get();

        return view('deepseek', compact('histories'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'prompt' => 'required'
        ]);

        $prompt = strtolower(trim($request->prompt));

        // Fake AI Response
        if (str_contains($prompt, 'laravel')) {

            $response = "Laravel is an open-source PHP framework used to build modern web applications.";

        } elseif (str_contains($prompt, 'php')) {

            $response = "PHP is a server-side scripting language widely used for web development.";

        } elseif (str_contains($prompt, 'ai')) {

            $response = "Artificial Intelligence enables machines to simulate human intelligence.";

        } else {

            $response = "AI Demo Response: " . $prompt;
        }

        // Save History
        ChatHistory::create([
            'prompt' => $prompt,
            'response' => $response
        ]);

        $histories = ChatHistory::latest()->take(10)->get();

        return view('deepseek', [
            'result' => $response,
            'prompt' => $prompt,
            'histories' => $histories
        ]);
    }
}