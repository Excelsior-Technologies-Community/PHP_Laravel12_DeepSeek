<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatHistory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DeepSeekController extends Controller
{
    private $apiKey;
    private $apiUrl = 'https://api.deepseek.com/v1/chat/completions';
    private $useDemoMode;

    public function __construct()
    {
        // Get API key from .env file
        $this->apiKey = env('DEEPSEEK_API_KEY', '');
        $this->useDemoMode = empty($this->apiKey) || env('DEEPSEEK_DEMO_MODE', true);
        
        // Log if we're in demo mode
        if ($this->useDemoMode) {
            Log::info('DeepSeek running in DEMO mode. Set DEEPSEEK_API_KEY in .env to enable real API calls.');
        }
    }

    public function showForm(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter', 'all');
        
        $query = ChatHistory::query();
        
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('prompt', 'like', "%{$search}%")
                  ->orWhere('response', 'like', "%{$search}%");
            });
        }
        
        $histories = $query->latest()->paginate(15);
        
        $stats = [
            'total_chats' => ChatHistory::count(),
            'total_tokens' => ChatHistory::sum('tokens_used'),
            'today_chats' => ChatHistory::whereDate('created_at', today())->count(),
            'failed_count' => ChatHistory::where('status', 'failed')->count()
        ];
        
        return view('deepseek', compact('histories', 'stats', 'search', 'filter'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:1|max:2000'
        ]);

        $prompt = trim($request->prompt);
        
        try {
            // Call DeepSeek API or use demo mode
            $response = $this->getAIResponse($prompt);
            
            // Save successful response
            $chatHistory = ChatHistory::create([
                'prompt' => $prompt,
                'response' => $response,
                'status' => 'completed',
                'model_used' => $this->useDemoMode ? 'demo-mode' : 'deepseek-chat',
                'tokens_used' => $this->estimateTokens($prompt, $response)
            ]);
            
            return redirect()->route('deepseek.result', $chatHistory->id)
                ->with('success', $this->useDemoMode ? 'Response generated in DEMO mode! (Add API key for real AI responses)' : 'Response generated successfully!');
            
        } catch (\Exception $e) {
            Log::error('DeepSeek Error: ' . $e->getMessage());
            
            // Save failed attempt
            ChatHistory::create([
                'prompt' => $prompt,
                'response' => 'Error: ' . $e->getMessage(),
                'status' => 'failed',
                'model_used' => 'error'
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to generate response: ' . $e->getMessage());
        }
    }
    
    private function getAIResponse($prompt)
    {
        if (!$this->useDemoMode && !empty($this->apiKey)) {
            try {
                return $this->callDeepSeekAPI($prompt);
            } catch (\Exception $e) {
                Log::warning('API call failed, falling back to demo mode: ' . $e->getMessage());
                return $this->getDemoResponse($prompt);
            }
        }
        
        return $this->getDemoResponse($prompt);
    }
    
    private function callDeepSeekAPI($prompt)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->timeout(30)->post($this->apiUrl, [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are DeepSeek AI, a helpful assistant. Provide accurate, concise, and helpful responses.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? 'No response from API';
        }
        
        $error = $response->json();
        $errorMessage = $error['error']['message'] ?? 'Unknown API error';
        throw new \Exception('API Error: ' . $errorMessage);
    }
    
    private function getDemoResponse($prompt)
    {
        $promptLower = strtolower($prompt);
        
        // Comprehensive demo responses
        $responses = [
            'laravel' => "**Laravel Framework**\n\nLaravel is a free, open-source PHP web framework created by Taylor Otwell. It follows the MVC architectural pattern and provides elegant syntax.\n\n**Key Features:**\n• Eloquent ORM for database interaction\n• Blade templating engine\n• Artisan command-line tool\n• Built-in authentication system\n• Robust routing system\n• Queue management\n• API development support\n\nLaravel is widely used for building modern web applications and APIs with clean, maintainable code.",
            
            'php' => "**PHP - Hypertext Preprocessor**\n\nPHP is a popular general-purpose scripting language especially suited for web development. It was created by Rasmus Lerdorf in 1994.\n\n**Key Features:**\n• Server-side scripting\n• Database integration (MySQL, PostgreSQL, etc.)\n• Cross-platform compatibility\n• Large community and ecosystem\n• Powers major CMS platforms (WordPress, Drupal, Joomla)\n• Framework support (Laravel, Symfony, CodeIgniter)\n\nPHP remains one of the most widely used backend languages powering over 75% of websites.",
            
            'ai' => "**Artificial Intelligence (AI)**\n\nArtificial Intelligence refers to the simulation of human intelligence in machines programmed to think and learn.\n\n**Main Branches:**\n• **Machine Learning** - Algorithms that learn from data\n• **Deep Learning** - Neural networks with multiple layers\n• **Natural Language Processing** - Understanding human language\n• **Computer Vision** - Interpreting visual information\n• **Robotics** - Physical machines with AI capabilities\n\n**Applications:** Healthcare, finance, autonomous vehicles, virtual assistants, recommendation systems, and more.",
            
            'deepseek' => "**DeepSeek AI Assistant**\n\nHello! I'm DeepSeek, an AI assistant designed to help you with various tasks including:\n\n• Answering questions on any topic\n• Writing and editing content\n• Code development and debugging\n• Data analysis and research\n• Creative writing and brainstorming\n• Learning and tutoring\n\nI'm here to provide accurate, helpful, and thoughtful responses. What would you like to know more about?",
            
            'database' => "**Database Management**\n\nA database is an organized collection of structured information stored electronically.\n\n**Popular Database Systems:**\n• **MySQL** - Open-source, widely used with PHP\n• **PostgreSQL** - Advanced, feature-rich open-source\n• **MongoDB** - NoSQL document database\n• **SQLite** - Lightweight, file-based database\n• **Redis** - In-memory key-value store\n\n**Key Concepts:** CRUD operations, indexing, relationships, transactions, normalization, and query optimization.",
            
            'api' => "**API (Application Programming Interface)**\n\nAn API allows different software applications to communicate with each other.\n\n**Types of APIs:**\n• **REST API** - Uses HTTP methods (GET, POST, PUT, DELETE)\n• **GraphQL** - Query language for APIs\n• **SOAP** - Protocol-based web services\n• **WebSocket** - Real-time bidirectional communication\n\n**Best Practices:** Authentication, rate limiting, versioning, documentation, error handling, and security measures.",
            
            'security' => "**Web Security Best Practices**\n\nEssential security measures for web applications:\n\n• **Input Validation** - Sanitize all user inputs\n• **CSRF Protection** - Use anti-forgery tokens\n• **XSS Prevention** - Escape output properly\n• **SQL Injection Prevention** - Use parameterized queries\n• **Authentication** - Implement strong password policies\n• **HTTPS** - Encrypt all data in transit\n• **Rate Limiting** - Prevent brute force attacks\n• **Regular Updates** - Keep dependencies current"
        ];
        
        // Check for keywords in prompt
        foreach ($responses as $key => $response) {
            if (str_contains($promptLower, $key)) {
                return $response;
            }
        }
        
        // Smart response for general queries
        if (strlen($prompt) < 10) {
            return "**Hello! I'm DeepSeek AI** 👋\n\nI notice you asked a short question. Could you please provide more details about what you'd like to know?\n\n**You can ask me about:**\n• Programming and coding\n• Web development (Laravel, PHP)\n• Artificial Intelligence\n• Databases and APIs\n• Security best practices\n• General knowledge questions\n\nFeel free to ask anything, and I'll do my best to help!";
        }
        
        // General response for other queries
        return "**Thank you for your question!** 🤖\n\nYou asked: *\"" . substr($prompt, 0, 150) . (strlen($prompt) > 150 ? '...' : '') . "\"*\n\n" .
               "I understand you're interested in this topic. For the most accurate and comprehensive response using the actual DeepSeek AI model:\n\n" .
               "**🔧 Setup Instructions:**\n" .
               "1. Get your free API key from [DeepSeek Platform](https://platform.deepseek.com/)\n" .
               "2. Add `DEEPSEEK_API_KEY=your_key_here` to your `.env` file\n" .
               "3. Set `DEEPSEEK_DEMO_MODE=false` in `.env`\n" .
               "4. Run `php artisan config:clear`\n\n" .
               "**💡 Demo Response:**\n\n" .
               "Based on your query about AI and technology, here are some key points:\n\n" .
               "• **Modern AI systems** use machine learning and neural networks\n" .
               "• **Natural Language Processing** enables human-like conversations\n" .
               "• **Deep learning** models can process complex patterns\n" .
               "• **API integration** allows AI to power various applications\n\n" .
               "For a more detailed, personalized response, please configure your API key. I'm ready to provide in-depth answers to all your questions!";
    }
    
    private function estimateTokens($prompt, $response)
    {
        // Approximate token count (rough estimate)
        return (int)((strlen($prompt) + strlen($response)) / 4);
    }
    
    public function showResult($id)
    {
        $chat = ChatHistory::findOrFail($id);
        $histories = ChatHistory::latest()->paginate(15);
        $stats = [
            'total_chats' => ChatHistory::count(),
            'total_tokens' => ChatHistory::sum('tokens_used'),
            'today_chats' => ChatHistory::whereDate('created_at', today())->count(),
            'failed_count' => ChatHistory::where('status', 'failed')->count()
        ];
        
        return view('deepseek', [
            'result' => $chat->response,
            'prompt' => $chat->prompt,
            'histories' => $histories,
            'currentChat' => $chat,
            'stats' => $stats,
            'search' => null,
            'filter' => 'all'
        ]);
    }
    
    public function retry(Request $request, $id)
    {
        $failedChat = ChatHistory::findOrFail($id);
        
        if ($failedChat->status !== 'failed') {
            return redirect()->back()->with('error', 'Only failed chats can be retried.');
        }
        
        $request->merge(['prompt' => $failedChat->prompt]);
        
        return $this->process($request);
    }
    
    public function deleteHistory($id)
    {
        $chat = ChatHistory::findOrFail($id);
        $chat->delete();
        
        return redirect()->route('deepseek.form')
            ->with('success', 'Chat history deleted successfully!');
    }
    
    public function clearAllHistory()
    {
        ChatHistory::truncate();
        
        return redirect()->route('deepseek.form')
            ->with('success', 'All chat history cleared successfully!');
    }
    
    public function exportHistory()
    {
        $histories = ChatHistory::all(['prompt', 'response', 'status', 'created_at']);
        
        $csvData = "Prompt,Response,Status,Created At\n";
        
        foreach ($histories as $history) {
            $csvData .= '"' . str_replace('"', '""', $history->prompt) . '",';
            $csvData .= '"' . str_replace('"', '""', $history->response) . '",';
            $csvData .= $history->status . ',';
            $csvData .= $history->created_at . "\n";
        }
        
        $fileName = 'deepseek_chat_history_' . date('Y-m-d_H-i-s') . '.csv';
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}