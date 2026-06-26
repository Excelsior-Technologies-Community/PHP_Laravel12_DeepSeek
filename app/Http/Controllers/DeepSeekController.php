<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatHistory;
use Illuminate\Support\Facades\Log;

class DeepSeekController extends Controller
{
    private string $apiKey;
    private string $apiUrl;

    private array $models = [
        'deepseek-chat'     => 'DeepSeek Chat (Fast)',
        'deepseek-reasoner' => 'DeepSeek R1 (Advanced Reasoning)',
    ];

    public function __construct()
    {
        // FIX 1: trim() to remove any accidental spaces in API key
        $this->apiKey = trim(env('DEEPSEEK_API_KEY', ''));

        // FIX 2: Correct endpoint URL construction
        // Official: https://api.deepseek.com/chat/completions
        $baseUrl = rtrim(trim(env('DEEPSEEK_ENDPOINT', 'https://api.deepseek.com')), '/');
        $this->apiUrl = $baseUrl . '/chat/completions';
    }

    // ─── Main Form ───────────────────────────────────────────
    public function showForm(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter', 'all');

        $query = ChatHistory::query();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('prompt', 'like', "%{$search}%")
                  ->orWhere('response', 'like', "%{$search}%");
            });
        }

        $histories = $query->latest()->paginate(10);

        $stats = [
            'total_chats'  => ChatHistory::count(),
            'total_tokens' => ChatHistory::sum('tokens_used') ?? 0,
            'today_chats'  => ChatHistory::whereDate('created_at', today())->count(),
            'failed_count' => ChatHistory::where('status', 'failed')->count(),
        ];

        return view('deepseek', compact('histories', 'stats', 'search', 'filter'));
    }

    // ─── SSE Streaming Endpoint ───────────────────────────────
    public function stream(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|min:1|max:5000',
            'model'  => 'nullable|string',
        ]);

        $prompt = trim($request->prompt);
        $model  = in_array($request->model, array_keys($this->models))
            ? $request->model
            : 'deepseek-chat';

        $chat = ChatHistory::create([
            'prompt'     => $prompt,
            'response'   => '',
            'status'     => 'processing',
            'model_used' => $model,
        ]);

        return response()->stream(function () use ($prompt, $model, $chat) {

            $fullResponse = '';

            try {
                if (empty($this->apiKey)) {
                    // Demo streaming mode (no API key)
                    $demoText = $this->getDemoResponse($prompt);
                    $words    = explode(' ', $demoText);

                    foreach ($words as $i => $word) {
                        $chunk = ($i === 0 ? '' : ' ') . $word;
                        $fullResponse .= $chunk;

                        echo "data: " . json_encode(['content' => $chunk, 'done' => false]) . "\n\n";
                        ob_flush();
                        flush();
                        usleep(40000); // 40ms delay per word
                    }
                } else {
                    // Real DeepSeek Streaming API
                    $ch = curl_init();

                    curl_setopt_array($ch, [
                        CURLOPT_URL            => $this->apiUrl,
                        CURLOPT_POST           => true,
                        CURLOPT_HTTPHEADER     => [
                            'Authorization: Bearer ' . $this->apiKey,
                            'Content-Type: application/json',
                            'Accept: text/event-stream',
                        ],
                        CURLOPT_POSTFIELDS     => json_encode([
                            'model'    => $model,
                            'stream'   => true,
                            'messages' => [
                                ['role' => 'system', 'content' => 'You are DeepSeek AI, a helpful, accurate assistant.'],
                                ['role' => 'user',   'content' => $prompt],
                            ],
                            'temperature' => 0.7,
                            'max_tokens'  => 4000,
                        ]),
                        CURLOPT_WRITEFUNCTION  => function ($curl, $data) use (&$fullResponse) {
                            $lines = explode("\n", $data);

                            foreach ($lines as $line) {
                                $line = trim($line);

                                // Skip empty lines and SSE comments (like : keep-alive)
                                if (empty($line) || str_starts_with($line, ':')) continue;
                                if (!str_starts_with($line, 'data: ')) continue;

                                $json = trim(substr($line, 6));
                                if ($json === '[DONE]') continue;

                                $decoded = json_decode($json, true);
                                if (json_last_error() !== JSON_ERROR_NONE) continue;

                                // FIX 3: Handle both delta.content and choices[0].message.content
                                $content = '';
                                if (isset($decoded['choices'][0]['delta']['content'])) {
                                    $content = $decoded['choices'][0]['delta']['content'];
                                } elseif (isset($decoded['choices'][0]['message']['content'])) {
                                    $content = $decoded['choices'][0]['message']['content'];
                                }

                                if ($content !== '') {
                                    $fullResponse .= $content;
                                    echo "data: " . json_encode(['content' => $content, 'done' => false]) . "\n\n";
                                    ob_flush();
                                    flush();
                                }

                                // FIX 4: Handle API errors in stream
                                if (isset($decoded['error'])) {
                                    $errMsg = $decoded['error']['message'] ?? 'Unknown API error';
                                    echo "data: " . json_encode(['error' => $errMsg, 'done' => true]) . "\n\n";
                                    ob_flush();
                                    flush();
                                    return strlen($data);
                                }
                            }
                            return strlen($data);
                        },
                        CURLOPT_RETURNTRANSFER => false,
                        CURLOPT_TIMEOUT        => 120,
                        CURLOPT_SSL_VERIFYPEER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_CONNECTTIMEOUT => 30,
                    ]);

                    $execResult = curl_exec($ch);
                    $curlError = curl_error($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($curlError) {
                        throw new \Exception('Connection Error: ' . $curlError);
                    }

                    if ($httpCode >= 400) {
                        throw new \Exception('API Error HTTP ' . $httpCode . '. Check your API key and endpoint URL.');
                    }
                }

                // Save successful response
                $chat->update([
                    'response'    => $fullResponse,
                    'status'      => 'completed',
                    'tokens_used' => (int)((strlen($prompt) + strlen($fullResponse)) / 4),
                ]);

                echo "data: " . json_encode(['done' => true, 'chat_id' => $chat->id]) . "\n\n";
                ob_flush();
                flush();

            } catch (\Exception $e) {
                Log::error('DeepSeek Stream Error: ' . $e->getMessage());

                $chat->update([
                    'response' => 'Error: ' . $e->getMessage(),
                    'status'   => 'failed',
                ]);

                echo "data: " . json_encode(['error' => $e->getMessage(), 'done' => true]) . "\n\n";
                ob_flush();
                flush();
            }

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    // ─── Result Page ──────────────────────────────────────────
    public function showResult($id)
    {
        $chat      = ChatHistory::findOrFail($id);
        $histories = ChatHistory::latest()->paginate(10);

        $stats = [
            'total_chats'  => ChatHistory::count(),
            'total_tokens' => ChatHistory::sum('tokens_used') ?? 0,
            'today_chats'  => ChatHistory::whereDate('created_at', today())->count(),
            'failed_count' => ChatHistory::where('status', 'failed')->count(),
        ];

        return view('deepseek', [
            'result'      => $chat->response,
            'prompt'      => $chat->prompt,
            'histories'   => $histories,
            'currentChat' => $chat,
            'stats'       => $stats,
            'search'      => null,
            'filter'      => 'all',
        ]);
    }

    // ─── Retry Failed Chat ────────────────────────────────────
    public function retry(Request $request, $id)
    {
        $failedChat = ChatHistory::findOrFail($id);

        if ($failedChat->status !== 'failed') {
            return back()->with('error', 'Only failed chats can be retried.');
        }

        return redirect('/')->with('retry_prompt', $failedChat->prompt);
    }

    // ─── Delete ───────────────────────────────────────────────
    public function deleteHistory($id)
    {
        ChatHistory::findOrFail($id)->delete();
        return redirect()->route('deepseek.form')->with('success', 'Chat deleted successfully!');
    }

    // ─── Clear All ────────────────────────────────────────────
    public function clearAllHistory()
    {
        ChatHistory::truncate();
        return redirect()->route('deepseek.form')->with('success', 'All history cleared!');
    }

    // ─── Export CSV ───────────────────────────────────────────
    public function exportHistory()
    {
        $histories = ChatHistory::all(['prompt', 'response', 'status', 'model_used', 'created_at']);

        $csv = "Prompt,Response,Status,Model,Created At\n";

        foreach ($histories as $h) {
            $csv .= '"' . str_replace('"', '""', $h->prompt) . '",';
            $csv .= '"' . str_replace('"', '""', $h->response) . '",';
            $csv .= $h->status . ',';
            $csv .= $h->model_used . ',';
            $csv .= $h->created_at . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="deepseek_history_' . now()->format('Y-m-d') . '.csv"');
    }

    // ─── Share Chat ───────────────────────────────────────────
    public function shareChat(Request $request, $id)
    {
        $chat  = ChatHistory::findOrFail($id);
        $token = $chat->share_token ?? $chat->generateShareToken();

        return response()->json([
            'share_url' => route('deepseek.shared', $token),
        ]);
    }

    // ─── View Shared Chat ─────────────────────────────────────
    public function viewSharedChat($token)
    {
        $chat = ChatHistory::where('share_token', $token)->firstOrFail();
        return view('shared-chat', compact('chat'));
    }

    // ─── Demo Responses ───────────────────────────────────────
    private function getDemoResponse(string $prompt): string
    {
        $lower = strtolower($prompt);

        $map = [
            'laravel'  => "**Laravel Framework**\n\nLaravel is a free, open-source PHP web framework by Taylor Otwell.\n\n**Key Features:**\n• Eloquent ORM\n• Blade Templating\n• Artisan CLI\n• Built-in Auth\n• Queue & Jobs\n• API Development\n\nLaravel follows MVC pattern and is ideal for modern web applications.",
            'php'      => "**PHP - Hypertext Preprocessor**\n\nPHP is a popular server-side scripting language for web development.\n\n**Features:**\n• Easy MySQL/PostgreSQL integration\n• Cross-platform\n• Powers 75%+ of web (WordPress, etc.)\n• Frameworks: Laravel, Symfony, CodeIgniter",
            'ai'       => "**Artificial Intelligence**\n\nAI simulates human intelligence in machines.\n\n**Branches:**\n• Machine Learning\n• Deep Learning\n• NLP\n• Computer Vision\n• Robotics\n\n**Applications:** Healthcare, finance, autonomous vehicles, virtual assistants.",
            'python'   => "**Python**\n\nPython is a versatile, beginner-friendly programming language.\n\n**Use Cases:**\n• Data Science & ML (pandas, numpy, sklearn)\n• Web Dev (Django, FastAPI)\n• Automation & Scripting\n• AI/ML Research\n\nKnown for readable syntax and massive ecosystem.",
            'database' => "**Database Systems**\n\n**Popular Options:**\n• MySQL - Most popular with PHP\n• PostgreSQL - Advanced features\n• MongoDB - NoSQL documents\n• Redis - In-memory cache\n• SQLite - Lightweight\n\n**Key Concepts:** CRUD, indexing, normalization, transactions.",
        ];

        foreach ($map as $key => $response) {
            if (str_contains($lower, $key)) {
                return $response;
            }
        }

        return "**AI Response** 🤖\n\nYou asked: *\"{$prompt}\"*\n\nThis is a **demo response** since no API key is configured.\n\n**To enable real AI:**\n1. Get API key from platform.deepseek.com\n2. Add `DEEPSEEK_API_KEY=your_key` to `.env`\n3. Run `php artisan config:clear`\n\nI can help with coding, writing, analysis, research, and much more!";
    }
}