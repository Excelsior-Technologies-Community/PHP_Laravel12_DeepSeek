<!DOCTYPE html>
<html lang="en" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DeepSeek AI Assistant</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg:       #0d1117;
            --surface:  #161b22;
            --surface2: #21262d;
            --border:   #30363d;
            --text:     #e6edf3;
            --muted:    #7d8590;
            --accent:   #7c3aed;
            --accent2:  #6366f1;
            --green:    #10b981;
            --red:      #ef4444;
            --yellow:   #f59e0b;
        }
        .light {
            --bg:       #f6f8fa;
            --surface:  #ffffff;
            --surface2: #f0f3f6;
            --border:   #d0d7de;
            --text:     #1f2328;
            --muted:    #656d76;
            --accent:   #7c3aed;
            --accent2:  #4f46e5;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            transition: background .3s, color .2s;
        }

        /* ── App Layout ── */
        .app-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Sidebar ── */
        .sidebar {
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding: 20px 14px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 8px;
        }
        .logo-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--accent2), var(--accent));
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .sidebar-logo h2 { font-size: 16px; font-weight: 700; line-height: 1.2; }
        .sidebar-logo p  { font-size: 11px; color: var(--muted); }

        .section-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: 0 8px;
            margin-bottom: 6px;
        }

        .stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 9px 12px;
            border-radius: 8px;
            background: var(--surface2);
            font-size: 13px;
            margin-bottom: 6px;
        }
        .stat-row .s-label { color: var(--muted); }
        .stat-row .s-value { font-weight: 700; color: var(--accent2); }
        .stat-row .s-value.danger { color: var(--red); }

        .sidebar-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 13px;
            cursor: pointer;
            transition: .2s;
            font-family: inherit;
            text-align: left;
            margin-bottom: 6px;
        }
        .sidebar-btn:hover { background: var(--surface2); }
        .sidebar-btn.danger { border-color: rgba(239,68,68,.5); color: var(--red); }
        .sidebar-btn.danger:hover { background: rgba(239,68,68,.08); }

        .recent-chats { flex: 1; overflow-y: auto; min-height: 0; }

        .history-card {
            padding: 9px 11px;
            border-radius: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: .2s;
            margin-bottom: 5px;
        }
        .history-card:hover { border-color: var(--accent2); background: var(--surface); }
        .hc-prompt {
            font-size: 12px;
            font-weight: 500;
            color: var(--text);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 4px;
        }
        .hc-meta { display: flex; align-items: center; gap: 6px; }
        .hc-badge {
            font-size: 10px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 20px;
        }
        .hc-badge.done { background: rgba(16,185,129,.15); color: var(--green); }
        .hc-badge.fail { background: rgba(239,68,68,.15);  color: var(--red); }
        .hc-badge.proc { background: rgba(245,158,11,.15); color: var(--yellow); }
        .hc-time { font-size: 11px; color: var(--muted); }

        /* ── Main Panel ── */
        .main-panel {
            display: flex;
            flex-direction: column;
            height: 100vh;
            overflow: hidden;
        }

        /* ── Topbar ── */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 20px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .topbar-title { font-size: 15px; font-weight: 600; white-space: nowrap; }

        .model-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 7px 12px;
            font-size: 13px;
            color: var(--text);
        }
        .model-pill select {
            background: transparent;
            border: none;
            color: var(--text);
            font-size: 13px;
            font-family: inherit;
            outline: none;
            cursor: pointer;
        }
        .model-pill select option { background: var(--surface); }

        .m-badge {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--accent2), var(--accent));
            color: white;
            font-weight: 700;
            white-space: nowrap;
        }

        .search-wrap {
            position: relative;
            flex-shrink: 0;
        }
        .search-wrap input {
            width: 180px;
            padding: 7px 32px 7px 12px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: .2s;
        }
        .search-wrap input:focus { border-color: var(--accent2); }
        .search-wrap i {
            position: absolute;
            right: 10px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 12px;
            pointer-events: none;
        }

        .icon-btn {
            width: 34px; height: 34px;
            border-radius: 8px;
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 15px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: .2s;
            flex-shrink: 0;
        }
        .icon-btn:hover { background: var(--border); }

        /* ── Messages Area ── */
        .messages-wrap {
            flex: 1;
            overflow-y: auto;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* ── Welcome Screen ── */
        .welcome {
            margin: auto;
            text-align: center;
            padding: 40px 20px;
            max-width: 480px;
        }
        .welcome-icon { font-size: 52px; margin-bottom: 14px; }
        .welcome h2 { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .welcome p { font-size: 14px; color: var(--muted); margin-bottom: 20px; line-height: 1.6; }
        .suggestions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }
        .suggestion-chip {
            padding: 8px 16px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 20px;
            color: var(--text);
            font-size: 13px;
            cursor: pointer;
            transition: .2s;
            font-family: inherit;
        }
        .suggestion-chip:hover { border-color: var(--accent2); background: var(--surface); }

        /* ── Message Row ── */
        .msg-row {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
            width: 100%;
        }

        /* User messages: right aligned */
        .msg-row.user {
            flex-direction: row-reverse;
            justify-content: flex-start;
        }

        /* AI messages: left aligned */
        .msg-row.ai {
            flex-direction: row;
            justify-content: flex-start;
        }

        .msg-avatar {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            align-self: flex-start;
            margin-top: 2px;
        }
        .msg-row.user  .msg-avatar { background: linear-gradient(135deg, var(--accent2), var(--accent)); }
        .msg-row.ai    .msg-avatar { background: var(--surface2); border: 1px solid var(--border); }

        .msg-content { max-width: 70%; display: flex; flex-direction: column; }
        .msg-row.user .msg-content { align-items: flex-end; }
        .msg-row.ai   .msg-content { align-items: flex-start; }

        .msg-bubble {
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.7;
            white-space: pre-wrap;
            word-break: break-word;
            max-width: 100%;
        }

        /* User bubble: purple gradient, right side */
        .msg-row.user .msg-bubble {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border-bottom-right-radius: 4px;
        }

        /* AI bubble: dark surface, left side */
        .msg-row.ai .msg-bubble {
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            border-bottom-left-radius: 4px;
        }

        .msg-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 5px;
            font-size: 11px;
            color: var(--muted);
            flex-wrap: wrap;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 11px;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 5px;
            transition: .2s;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .action-btn:hover { background: var(--border); color: var(--text); }

        /* ── Typing dots ── */
        .typing-dots {
            display: flex;
            gap: 4px;
            padding: 4px 0;
        }
        .typing-dots span {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--accent);
            animation: tdot 1.2s infinite ease-in-out;
        }
        .typing-dots span:nth-child(2) { animation-delay: .2s; }
        .typing-dots span:nth-child(3) { animation-delay: .4s; }
        @keyframes tdot { 0%,80%,100%{transform:scale(0)} 40%{transform:scale(1)} }

        /* Streaming cursor */
        .streaming-cursor::after {
            content: '▋';
            color: var(--accent);
            animation: cblink .7s infinite;
        }
        @keyframes cblink { 0%,100%{opacity:1} 50%{opacity:0} }

        /* ── Flash ── */
        .flash {
            padding: 11px 14px;
            border-radius: 9px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }
        .flash.success { background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.25); color: var(--green); }
        .flash.error   { background: rgba(239,68,68,.12);  border: 1px solid rgba(239,68,68,.25);  color: var(--red); }

        /* ── Input Area ── */
        .input-area {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 14px 20px;
            flex-shrink: 0;
        }

        .voice-bar {
            display: none;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--red);
            margin-bottom: 8px;
        }
        .voice-bar.show { display: flex; }
        .v-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--red);
            animation: vpulse 1s infinite;
        }
        @keyframes vpulse { 0%,100%{opacity:1} 50%{opacity:.3} }

        .input-box {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            background: var(--surface2);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            padding: 10px 12px;
            transition: border-color .2s;
        }
        .input-box:focus-within { border-color: var(--accent2); }

        .inp-btn {
            width: 34px; height: 34px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--muted);
            font-size: 15px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: .2s;
            flex-shrink: 0;
        }
        .inp-btn:hover { border-color: var(--accent2); color: var(--text); }
        .inp-btn:disabled { opacity: .4; cursor: not-allowed; }

        .inp-btn#voice-btn.recording {
            background: rgba(239,68,68,.15);
            border-color: var(--red);
            color: var(--red);
            animation: rpulse 1.5s infinite;
        }
        @keyframes rpulse { 0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.3)} 50%{box-shadow:0 0 0 7px rgba(239,68,68,0)} }

        .inp-btn#tts-btn.active { color: var(--green); border-color: var(--green); }

        #send-btn {
            background: linear-gradient(135deg, var(--accent2), var(--accent));
            border-color: transparent;
            color: white;
        }
        #send-btn:hover:not(:disabled) { transform: scale(1.06); border-color: transparent; }

        #prompt-input {
            flex: 1;
            background: none;
            border: none;
            color: var(--text);
            font-size: 14px;
            font-family: inherit;
            resize: none;
            outline: none;
            min-height: 22px;
            max-height: 140px;
            line-height: 1.55;
        }
        #prompt-input::placeholder { color: var(--muted); }

        .input-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 7px;
            font-size: 11px;
            color: var(--muted);
        }
        #char-count.warn { color: var(--yellow); }

        /* ── Share Modal ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.55);
            z-index: 200;
            justify-content: center;
            align-items: center;
        }
        .modal-overlay.show { display: flex; }
        .modal-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 26px;
            max-width: 460px;
            width: 92%;
        }
        .modal-box h3 { font-size: 17px; margin-bottom: 8px; }
        .modal-box p  { font-size: 13px; color: var(--muted); margin-bottom: 14px; }
        .modal-input {
            width: 100%;
            padding: 9px 12px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 9px;
            color: var(--text);
            font-size: 13px;
            font-family: inherit;
            margin-bottom: 16px;
        }
        .modal-btns { display: flex; gap: 10px; justify-content: flex-end; }
        .modal-btn {
            padding: 8px 18px;
            border-radius: 9px;
            font-size: 13px;
            cursor: pointer;
            font-family: inherit;
            border: 1px solid var(--border);
            background: var(--surface2);
            color: var(--text);
            transition: .2s;
        }
        .modal-btn.primary {
            background: linear-gradient(135deg, var(--accent2), var(--accent));
            border-color: transparent;
            color: white;
        }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .app-layout { grid-template-columns: 1fr; }
            .sidebar { display: none; }
            .msg-content { max-width: 88%; }
        }
    </style>
</head>
<body>

<div class="app-layout">

    {{-- ════════════ SIDEBAR ════════════ --}}
    <aside class="sidebar">

        <div class="sidebar-logo">
            <div class="logo-icon">🤖</div>
            <div>
                <h2>DeepSeek AI</h2>
                <p>Powered by DeepSeek API</p>
            </div>
        </div>

        {{-- Stats --}}
        <div>
            <div class="section-label">Analytics</div>
            <div class="stat-row">
                <span class="s-label">Total Chats</span>
                <span class="s-value">{{ $stats['total_chats'] }}</span>
            </div>
            <div class="stat-row">
                <span class="s-label">Today</span>
                <span class="s-value">{{ $stats['today_chats'] }}</span>
            </div>
            <div class="stat-row">
                <span class="s-label">Tokens Used</span>
                <span class="s-value">{{ number_format($stats['total_tokens']) }}</span>
            </div>
            <div class="stat-row">
                <span class="s-label">Failed</span>
                <span class="s-value danger">{{ $stats['failed_count'] }}</span>
            </div>
        </div>

        {{-- Actions --}}
        <div>
            <div class="section-label">Actions</div>
            <button class="sidebar-btn" onclick="location.href='{{ route('deepseek.export') }}'">
                <i class="fas fa-download" style="color:var(--accent2)"></i> Export CSV
            </button>
            <button class="sidebar-btn danger" onclick="confirmClearAll()">
                <i class="fas fa-trash-alt"></i> Clear All History
            </button>
        </div>

        {{-- Recent Chats --}}
        <div class="recent-chats">
            <div class="section-label">Recent Chats</div>
            @foreach($histories->take(10) as $h)
                <div class="history-card" onclick="location.href='{{ route('deepseek.result', $h->id) }}'">
                    <div class="hc-prompt">{{ Str::limit($h->prompt, 45) }}</div>
                    <div class="hc-meta">
                        <span class="hc-badge {{ $h->status === 'completed' ? 'done' : ($h->status === 'failed' ? 'fail' : 'proc') }}">
                            {{ ucfirst($h->status) }}
                        </span>
                        <span class="hc-time">{{ $h->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>

    </aside>

    {{-- ════════════ MAIN PANEL ════════════ --}}
    <div class="main-panel">

        {{-- Topbar --}}
        <header class="topbar">
            <span class="topbar-title">💬 AI Chat Assistant</span>

            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">

                {{-- Model Selector --}}
                <div class="model-pill">
                    <i class="fas fa-microchip" style="color:var(--accent); font-size:12px;"></i>
                    <select id="model-selector" onchange="updateModelBadge()">
                        <option value="deepseek-chat">DeepSeek Chat</option>
                        <option value="deepseek-reasoner">DeepSeek R1 (Reasoning)</option>
                    </select>
                    <span class="m-badge" id="model-badge">Fast</span>
                </div>

                {{-- Search --}}
                <form method="GET" action="/">
                    <div class="search-wrap">
                        <input type="text" name="search" placeholder="Search history…" value="{{ request('search') }}">
                        <i class="fas fa-search"></i>
                    </div>
                </form>

                {{-- Theme --}}
                <button class="icon-btn" id="theme-btn" onclick="toggleTheme()" title="Toggle theme">🌙</button>

            </div>
        </header>

        {{-- Messages --}}
        <div class="messages-wrap" id="messages-wrap">

            {{-- Flash --}}
            @if(session('success'))
                <div class="flash success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
            @endif

            @if(isset($result) && $result)

                {{-- ── User Message ── --}}
                <div class="msg-row user">
                    <div class="msg-avatar">👤</div>
                    <div class="msg-content">
                        <div class="msg-bubble">{{ $prompt ?? '' }}</div>
                        <div class="msg-meta">
                            @isset($currentChat) {{ $currentChat->model_used }} • @endisset
                            {{ isset($currentChat) ? $currentChat->created_at->diffForHumans() : 'Just now' }}
                        </div>
                    </div>
                </div>

                {{-- ── AI Response ── --}}
                <div class="msg-row ai">
                    <div class="msg-avatar">🤖</div>
                    <div class="msg-content">
                        <div class="msg-bubble" id="current-response">{{ $result }}</div>
                        <div class="msg-meta">
                            <button class="action-btn" onclick="copyText('current-response')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                            <button class="action-btn" onclick="speakText(document.getElementById('current-response').innerText)">
                                <i class="fas fa-volume-up"></i> Listen
                            </button>
                            @isset($currentChat)
                                <button class="action-btn" onclick="shareChat({{ $currentChat->id }})">
                                    <i class="fas fa-share-alt"></i> Share
                                </button>
                            @endisset
                        </div>
                    </div>
                </div>

            @else

                {{-- Welcome --}}
                <div class="welcome" id="welcome-screen">
                    <div class="welcome-icon">🤖</div>
                    <h2>DeepSeek AI Assistant</h2>
                    <p>Ask me anything! I can help with coding, analysis, writing, research, and much more.</p>
                    <div class="suggestions">
                        @foreach(['Explain quantum computing', 'Write a Python script', 'Help me debug Laravel', 'What is machine learning?', 'Write a cover letter'] as $s)
                            <button class="suggestion-chip" onclick="setPrompt('{{ $s }}')">{{ $s }}</button>
                        @endforeach
                    </div>
                </div>

            @endif

            {{-- Live Streaming Area (shown while streaming) --}}
            <div id="stream-row" style="display:none;">

                {{-- User question (shown during stream) --}}
                <div class="msg-row user" id="stream-user-row">
                    <div class="msg-avatar">👤</div>
                    <div class="msg-content">
                        <div class="msg-bubble" id="stream-user-text"></div>
                        <div class="msg-meta" id="stream-user-meta"></div>
                    </div>
                </div>

                {{-- AI streaming response --}}
                <div class="msg-row ai">
                    <div class="msg-avatar">🤖</div>
                    <div class="msg-content">
                        <div class="msg-bubble streaming-cursor" id="stream-output"></div>
                        <div id="typing-wrap" style="margin-top:6px;">
                            <div class="typing-dots" id="typing-dots">
                                <span></span><span></span><span></span>
                            </div>
                        </div>
                        <div class="msg-meta" id="stream-actions" style="display:none;">
                            <button class="action-btn" onclick="copyText('stream-output')">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                            <button class="action-btn" onclick="speakText(document.getElementById('stream-output').innerText)">
                                <i class="fas fa-volume-up"></i> Listen
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- Input Area --}}
        <div class="input-area">

            <div class="voice-bar" id="voice-bar">
                <div class="v-dot"></div>
                <span id="voice-bar-text">Listening…</span>
            </div>

            <div class="input-box">
                <button class="inp-btn" id="voice-btn" onclick="toggleVoice()" title="Voice input (Speech-to-Text)">🎤</button>

                <textarea
                    id="prompt-input"
                    placeholder="Ask me anything… (Enter to send, Shift+Enter for new line)"
                    rows="1"
                    maxlength="5000"
                >{{ session('retry_prompt', '') }}</textarea>

                <button class="inp-btn" id="tts-btn" onclick="toggleTTS()" title="Toggle auto Text-to-Speech">🔊</button>

                <button class="inp-btn" id="send-btn" onclick="sendMessage()" title="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            <div class="input-footer">
                <span>🎤 Mic = voice input &nbsp;|&nbsp; 🔊 Speaker = auto-read responses</span>
                <span id="char-count">0 / 5000</span>
            </div>

        </div>

    </div>
</div>

{{-- Share Modal --}}
<div class="modal-overlay" id="shareModal">
    <div class="modal-box">
        <h3>📤 Share Conversation</h3>
        <p>Anyone with this link can view this conversation.</p>
        <input type="text" class="modal-input" id="shareLink" readonly>
        <div class="modal-btns">
            <button class="modal-btn" onclick="closeShare()">Cancel</button>
            <button class="modal-btn primary" onclick="copyShareLink()">
                <i class="fas fa-copy"></i> Copy Link
            </button>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const msgsWrap = document.getElementById('messages-wrap');

// ── Scroll to bottom ─────────────────────────────────────────
function scrollBottom() {
    msgsWrap.scrollTop = msgsWrap.scrollHeight;
}
scrollBottom();

// ── Theme ────────────────────────────────────────────────────
function toggleTheme() {
    const light = document.documentElement.classList.toggle('light');
    document.getElementById('theme-btn').textContent = light ? '🌞' : '🌙';
    localStorage.setItem('ds-theme', light ? 'light' : 'dark');
}
(function initTheme() {
    if (localStorage.getItem('ds-theme') === 'light') {
        document.documentElement.classList.add('light');
        document.getElementById('theme-btn').textContent = '🌞';
    }
})();

// ── Model Badge ──────────────────────────────────────────────
function updateModelBadge() {
    const v = document.getElementById('model-selector').value;
    document.getElementById('model-badge').textContent = v === 'deepseek-reasoner' ? 'R1' : 'Fast';
}

// ── Textarea auto-resize ─────────────────────────────────────
const promptInput = document.getElementById('prompt-input');
const charCount   = document.getElementById('char-count');

promptInput.addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 140) + 'px';
    const n = this.value.length;
    charCount.textContent = n + ' / 5000';
    charCount.className = n > 4500 ? 'warn' : '';
});

promptInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

function setPrompt(text) {
    promptInput.value = text;
    promptInput.dispatchEvent(new Event('input'));
    promptInput.focus();
}

// ── Send / Stream ────────────────────────────────────────────
function sendMessage() {
    const prompt  = promptInput.value.trim();
    if (!prompt) return;

    const model   = document.getElementById('model-selector').value;
    const sendBtn = document.getElementById('send-btn');

    // Disable inputs
    sendBtn.disabled = true;
    promptInput.disabled = true;

    // Hide welcome screen
    const welcome = document.getElementById('welcome-screen');
    if (welcome) welcome.style.display = 'none';

    // Show streaming row
    const streamRow = document.getElementById('stream-row');
    const streamOut = document.getElementById('stream-output');
    const typDots   = document.getElementById('typing-dots');
    const streamAct = document.getElementById('stream-actions');
    const streamMeta= document.getElementById('stream-user-meta');

    document.getElementById('stream-user-text').textContent = prompt;
    streamMeta.textContent = model + ' • Just now';
    streamRow.style.display = 'block';
    streamOut.textContent   = '';
    streamOut.classList.add('streaming-cursor');
    typDots.style.display   = 'flex';
    streamAct.style.display = 'none';

    scrollBottom();

    // Fetch SSE stream
    fetch('{{ route("deepseek.stream") }}', {
        method:  'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  CSRF,
            'Accept':        'text/event-stream',
        },
        body: JSON.stringify({ prompt, model }),
    })
    .then(res => {
        const reader  = res.body.getReader();
        const decoder = new TextDecoder();
        let   buffer  = '';

        typDots.style.display = 'none';

        function read() {
            reader.read().then(({ done, value }) => {
                if (done) return;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');
                buffer = lines.pop();

                for (const line of lines) {
                    if (!line.startsWith('data: ')) continue;
                    const raw = line.slice(6).trim();
                    if (!raw) continue;

                    try {
                        const pkt = JSON.parse(raw);

                        if (pkt.error) {
                            streamOut.textContent += '\n\n⚠️ Error: ' + pkt.error;
                            finishStream(sendBtn);
                            return;
                        }

                        if (pkt.content) {
                            streamOut.textContent += pkt.content;
                            scrollBottom();
                        }

                        if (pkt.done) {
                            finishStream(sendBtn);
                            streamAct.style.display = 'flex';
                            if (ttsEnabled) speakText(streamOut.innerText);
                        }
                    } catch(e) {}
                }
                read();
            });
        }
        read();
    })
    .catch(err => {
        streamOut.textContent += '\n\n⚠️ Connection error: ' + err.message;
        finishStream(sendBtn);
    });
}

function finishStream(sendBtn) {
    document.getElementById('stream-output').classList.remove('streaming-cursor');
    document.getElementById('typing-dots').style.display = 'none';
    sendBtn.disabled = false;
    promptInput.disabled = false;
    promptInput.value = '';
    promptInput.style.height = 'auto';
    charCount.textContent = '0 / 5000';
    scrollBottom();
}

// ── Voice Input ──────────────────────────────────────────────
let recognition = null;
let isRecording  = false;

function toggleVoice() {
    if (!window.SpeechRecognition && !window.webkitSpeechRecognition) {
        alert('Voice input requires Chrome or Edge browser.');
        return;
    }
    isRecording ? stopVoice() : startVoice();
}

function startVoice() {
    const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SR();
    recognition.lang = 'en-US';
    recognition.continuous = false;
    recognition.interimResults = true;

    const vBtn = document.getElementById('voice-btn');
    const vBar = document.getElementById('voice-bar');
    const vTxt = document.getElementById('voice-bar-text');

    recognition.onstart = () => {
        isRecording = true;
        vBtn.classList.add('recording');
        vBtn.textContent = '⏹';
        vBar.classList.add('show');
        vTxt.textContent = 'Listening…';
    };

    recognition.onresult = (e) => {
        let fin = '', int = '';
        for (let i = e.resultIndex; i < e.results.length; i++) {
            const t = e.results[i][0].transcript;
            e.results[i].isFinal ? (fin += t) : (int += t);
        }
        if (fin) {
            promptInput.value += fin;
            promptInput.dispatchEvent(new Event('input'));
            vTxt.textContent = '✓ ' + fin;
        } else {
            vTxt.textContent = '… ' + int;
        }
    };

    recognition.onerror = (e) => { if (e.error !== 'no-speech') alert('Voice error: ' + e.error); stopVoice(); };
    recognition.onend   = stopVoice;
    recognition.start();
}

function stopVoice() {
    isRecording = false;
    try { recognition?.stop(); } catch(e) {}
    const vBtn = document.getElementById('voice-btn');
    vBtn.classList.remove('recording');
    vBtn.textContent = '🎤';
    setTimeout(() => document.getElementById('voice-bar').classList.remove('show'), 1200);
}

// ── Text-to-Speech ───────────────────────────────────────────
let ttsEnabled = false;

function toggleTTS() {
    ttsEnabled = !ttsEnabled;
    const btn = document.getElementById('tts-btn');
    btn.textContent = ttsEnabled ? '🔇' : '🔊';
    btn.classList.toggle('active', ttsEnabled);
    if (!ttsEnabled) speechSynthesis.cancel();
}

function speakText(text) {
    if (!window.speechSynthesis) return;
    speechSynthesis.cancel();
    const clean = text
        .replace(/\*\*(.*?)\*\*/g, '$1')
        .replace(/\*(.*?)\*/g, '$1')
        .replace(/#{1,6}\s/g, '')
        .replace(/`[^`]*`/g, '')
        .substring(0, 2000);
    const u = new SpeechSynthesisUtterance(clean);
    u.lang = 'en-US'; u.rate = 1.0;
    const voices = speechSynthesis.getVoices();
    const best = voices.find(v => v.name.includes('Google') || v.lang === 'en-US');
    if (best) u.voice = best;
    const btn = document.getElementById('tts-btn');
    u.onstart = () => { btn.textContent = '⏸'; btn.classList.add('active'); };
    u.onend   = () => { btn.textContent = ttsEnabled ? '🔇' : '🔊'; if (!ttsEnabled) btn.classList.remove('active'); };
    speechSynthesis.speak(u);
}

if (window.speechSynthesis) {
    speechSynthesis.getVoices();
    speechSynthesis.onvoiceschanged = () => speechSynthesis.getVoices();
}

// ── Copy ─────────────────────────────────────────────────────
function copyText(elId) {
    const text = document.getElementById(elId)?.innerText;
    if (text) { navigator.clipboard.writeText(text); showToast('Copied!'); }
}

// ── Share ─────────────────────────────────────────────────────
function shareChat(id) {
    fetch('/share/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(d => {
        document.getElementById('shareLink').value = d.share_url;
        document.getElementById('shareModal').classList.add('show');
    });
}
function copyShareLink() {
    navigator.clipboard.writeText(document.getElementById('shareLink').value);
    closeShare();
    showToast('Share link copied!');
}
function closeShare() { document.getElementById('shareModal').classList.remove('show'); }
window.addEventListener('click', e => {
    if (e.target === document.getElementById('shareModal')) closeShare();
});

// ── Clear All ─────────────────────────────────────────────────
function confirmClearAll() {
    if (confirm('⚠️ Delete ALL chat history? Cannot be undone.')) {
        location.href = '{{ route("deepseek.clear") }}';
    }
}

// ── Toast ─────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.style.cssText = `
        position:fixed; bottom:22px; left:50%; transform:translateX(-50%);
        padding:9px 20px; border-radius:9px; font-size:13px; z-index:9999;
        background:${type === 'success' ? 'var(--green)' : 'var(--red)'}; color:#fff;
        box-shadow:0 4px 18px rgba(0,0,0,.3); white-space:nowrap;
    `;
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2800);
}
</script>

</body>
</html>