<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DeepSeek AI Assistant - Smart AI Chatbot</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
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
            padding: 20px;
            transition: 0.3s;
        }

        body.light {
            background: #f3f4f6;
            color: #111827;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo h1 {
            font-size: 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo p {
            font-size: 12px;
            opacity: 0.7;
        }

        .header-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Card Styles */
        .card {
            background: #1e293b;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            transition: 0.3s;
        }

        body.light .card {
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: linear-gradient(135deg, #1e293b, #334155);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        body.light .stat-card {
            background: linear-gradient(135deg, #ffffff, #f3f4f6);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card i {
            font-size: 30px;
            color: #8b5cf6;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
        }

        .stat-label {
            font-size: 12px;
            opacity: 0.7;
            margin-top: 5px;
        }

        /* Form Styles */
        textarea {
            width: 100%;
            padding: 18px;
            border: 2px solid #334155;
            border-radius: 15px;
            background: #0f172a;
            color: white;
            font-size: 15px;
            resize: vertical;
            transition: 0.3s;
        }

        textarea:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 10px rgba(139, 92, 246, 0.3);
        }

        body.light textarea {
            background: white;
            color: #111827;
            border-color: #d1d5db;
        }

        /* Button Styles */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #334155;
            color: white;
        }

        body.light .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }

        /* Search & Filter */
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 2px solid #334155;
            border-radius: 12px;
            background: #0f172a;
            color: white;
        }

        body.light .search-box input {
            background: white;
            border-color: #d1d5db;
            color: #111827;
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b5cf6;
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 12px 20px;
            background: #334155;
            border: none;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        .filter-btn.active {
            background: #8b5cf6;
        }

        body.light .filter-btn {
            background: #e5e7eb;
            color: #111827;
        }

        /* Response Area */
        .response {
            background: #334155;
            padding: 25px;
            border-radius: 15px;
            margin-top: 20px;
            line-height: 1.8;
        }

        body.light .response {
            background: #f9fafb;
        }

        /* History Items */
        .history-item {
            background: #334155;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            transition: 0.3s;
        }

        .history-item:hover {
            transform: translateX(5px);
        }

        body.light .history-item {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: #10b981;
            color: white;
        }

        .badge-danger {
            background: #ef4444;
            color: white;
        }

        .history-actions {
            display: flex;
            gap: 8px;
        }

        .icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .icon-btn:hover {
            background: rgba(139, 92, 246, 0.2);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            padding: 8px 15px;
            background: #334155;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            transition: 0.3s;
        }

        body.light .pagination a, body.light .pagination span {
            background: #e5e7eb;
            color: #111827;
        }

        .pagination a:hover {
            background: #8b5cf6;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-success {
            background: #10b981;
            color: white;
        }

        .alert-error {
            background: #ef4444;
            color: white;
        }

        /* Toggle Button */
        .toggle-mode {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .modal-content {
            background: #1e293b;
            padding: 30px;
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
        }

        body.light .modal-content {
            background: white;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .history-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .typing-indicator {
            display: flex;
            gap: 5px;
            padding: 15px;
        }

        .typing-indicator span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #8b5cf6;
            animation: bounce 1.4s infinite ease-in-out;
        }

        .typing-indicator span:nth-child(1) { animation-delay: -0.32s; }
        .typing-indicator span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        .loader {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<button class="toggle-mode" onclick="toggleMode()">
    <i class="fas fa-moon"></i>
</button>

<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <h1><i class="fas fa-robot"></i> DeepSeek AI</h1>
            <p>Advanced AI Assistant powered by DeepSeek</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-secondary" onclick="exportHistory()">
                <i class="fas fa-download"></i> Export CSV
            </button>
            <button class="btn btn-danger" onclick="confirmClearAll()">
                <i class="fas fa-trash-alt"></i> Clear All
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success">
            <span><i class="fas fa-check-circle"></i> {{ session('success') }}</span>
            <i class="fas fa-times" style="cursor: pointer;" onclick="this.parentElement.remove()"></i>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <span><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</span>
            <i class="fas fa-times" style="cursor: pointer;" onclick="this.parentElement.remove()"></i>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <span><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</span>
            <i class="fas fa-times" style="cursor: pointer;" onclick="this.parentElement.remove()"></i>
        </div>
    @endif

   

    <!-- Main Chat Card -->
    <div class="card">
        <form method="POST" action="{{ route('deepseek.process') }}" onsubmit="showLoader(event)">
            @csrf
            <textarea 
                name="prompt" 
                placeholder="Ask me anything! I can help with coding, analysis, writing, research, and more..."
                rows="4"
            >{{ old('prompt') ?? ($prompt ?? '') }}</textarea>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; flex-wrap: wrap; gap: 10px;">
                <div style="font-size: 12px; opacity: 0.7;">
                    <i class="fas fa-info-circle"></i> Powered by DeepSeek AI Model
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Generate Response
                </button>
            </div>
        </form>

        <div class="loader" id="loader">
            <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
            </div>
            <p>AI is generating response...</p>
        </div>

        @if(isset($result))
            <div class="response">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                    <h3 style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-robot" style="color: #8b5cf6;"></i> 
                        AI Response
                    </h3>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn btn-secondary" onclick="copyText()" style="padding: 8px 16px;">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                        @if(isset($currentChat))
                            <button class="btn btn-secondary" onclick="shareChat({{ $currentChat->id }})" style="padding: 8px 16px;">
                                <i class="fas fa-share-alt"></i> Share
                            </button>
                        @endif
                    </div>
                </div>
                <div id="aiText" style="white-space: pre-wrap; line-height: 1.6;">{{ $result }}</div>
            </div>
        @endif
    </div>

    <!-- Chat History Section -->
    <div class="card">
        <h2 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-history"></i> Chat History
        </h2>
        
        <!-- Search and Filter -->
        <div class="search-filter">
            <div class="search-box">
                <form method="GET" action="{{ route('deepseek.form') }}" id="searchForm">
                    <input type="text" name="search" placeholder="Search conversations..." value="{{ request('search') }}">
                    <i class="fas fa-search"></i>
                    <input type="hidden" name="filter" value="{{ request('filter', 'all') }}">
                </form>
            </div>
            <div class="filter-buttons">
                <button class="filter-btn {{ request('filter', 'all') == 'all' ? 'active' : '' }}" onclick="filterHistory('all')">All</button>
                <button class="filter-btn {{ request('filter') == 'completed' ? 'active' : '' }}" onclick="filterHistory('completed')">Success</button>
                <button class="filter-btn {{ request('filter') == 'failed' ? 'active' : '' }}" onclick="filterHistory('failed')">Failed</button>
            </div>
        </div>

        @forelse($histories as $history)
            <div class="history-item">
                <div class="history-header">
                    <div>
                        <span class="badge {{ $history->status == 'completed' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($history->status) }}
                        </span>
                        <span style="font-size: 11px; margin-left: 10px;">
                            <i class="far fa-clock"></i> {{ $history->created_at->diffForHumans() }}
                        </span>
                        @if($history->tokens_used)
                            <span style="font-size: 11px; margin-left: 10px;">
                                <i class="fas fa-microchip"></i> {{ number_format($history->tokens_used) }} tokens
                            </span>
                        @endif
                    </div>
                    <div class="history-actions">
                        @if($history->status == 'failed')
                            <form method="POST" action="{{ route('deepseek.retry', $history->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="icon-btn" title="Retry">
                                    <i class="fas fa-redo-alt" style="color: #8b5cf6;"></i>
                                </button>
                            </form>
                        @endif
                        <button class="icon-btn" onclick="shareChat({{ $history->id }})" title="Share">
                            <i class="fas fa-share-alt" style="color: #10b981;"></i>
                        </button>
                        <button class="icon-btn" onclick="deleteChat({{ $history->id }})" title="Delete">
                            <i class="fas fa-trash" style="color: #ef4444;"></i>
                        </button>
                    </div>
                </div>
                <div style="margin-bottom: 10px;">
                    <strong><i class="fas fa-user"></i> You:</strong>
                    <p style="margin-top: 5px; opacity: 0.9;">{{ Str::limit($history->prompt, 200) }}</p>
                </div>
                <div>
                    <strong><i class="fas fa-robot"></i> AI:</strong>
                    <p style="margin-top: 5px; opacity: 0.9;">{{ Str::limit($history->response, 300) }}</p>
                </div>
                @if(strlen($history->response) > 300)
                    <button class="btn btn-secondary" onclick="viewFullResponse({{ $history->id }})" style="margin-top: 10px; padding: 5px 12px; font-size: 12px;">
                        <i class="fas fa-expand-alt"></i> View Full Response
                    </button>
                @endif
            </div>
        @empty
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-comments" style="font-size: 48px; opacity: 0.3;"></i>
                <p style="margin-top: 15px;">No chat history found. Start a conversation with DeepSeek AI!</p>
            </div>
        @endforelse

        <!-- Pagination -->
        @if(method_exists($histories, 'links'))
            <div class="pagination">
                {{ $histories->appends(['search' => request('search'), 'filter' => request('filter')])->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3 style="margin-bottom: 15px;">Delete Chat History</h3>
        <p>Are you sure you want to delete this conversation? This action cannot be undone.</p>
        <div class="modal-buttons">
            <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div id="shareModal" class="modal">
    <div class="modal-content">
        <h3 style="margin-bottom: 15px;">Share Conversation</h3>
        <p>Share this conversation using the link below:</p>
        <input type="text" id="shareLink" readonly style="width: 100%; margin: 15px 0; padding: 10px; border-radius: 8px; background: #334155; color: white; border: none;">
        <div class="modal-buttons">
            <button class="btn btn-secondary" onclick="closeShareModal()">Close</button>
            <button class="btn btn-primary" onclick="copyShareLink()">Copy Link</button>
        </div>
    </div>
</div>

<script>
    let deleteChatId = null;
    let currentShareId = null;

    function showLoader(event) {
        event.preventDefault();
        document.getElementById('loader').style.display = 'block';
        event.target.submit();
    }

    function copyText() {
        let text = document.getElementById('aiText')?.innerText;
        if (text) {
            navigator.clipboard.writeText(text);
            showNotification('Copied to clipboard!', 'success');
        }
    }

    function toggleMode() {
        document.body.classList.toggle('light');
        localStorage.setItem('theme', document.body.classList.contains('light') ? 'light' : 'dark');
    }

    // Load saved theme
    if (localStorage.getItem('theme') === 'light') {
        document.body.classList.add('light');
    }

    function filterHistory(filter) {
        window.location.href = '{{ route("deepseek.form") }}?filter=' + filter + '&search=' + encodeURIComponent('{{ request("search") }}');
    }

    function deleteChat(id) {
        deleteChatId = id;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function confirmDelete() {
        if (deleteChatId) {
            window.location.href = '{{ url("deepseek/delete") }}/' + deleteChatId;
        }
    }

    function confirmClearAll() {
        if (confirm('⚠️ WARNING: This will delete ALL chat history. This action cannot be undone!\n\nAre you sure you want to continue?')) {
            window.location.href = '{{ route("deepseek.clear") }}';
        }
    }

    function exportHistory() {
        window.location.href = '{{ route("deepseek.export") }}';
    }

    function shareChat(id) {
        currentShareId = id;
        
        fetch('{{ url("deepseek/share") }}/' + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('shareLink').value = data.share_url;
            document.getElementById('shareModal').style.display = 'flex';
        })
        .catch(error => {
            showNotification('Failed to generate share link', 'error');
        });
    }

    function copyShareLink() {
        const shareLink = document.getElementById('shareLink');
        shareLink.select();
        document.execCommand('copy');
        showNotification('Share link copied to clipboard!', 'success');
        closeShareModal();
    }

    function viewFullResponse(id) {
        window.location.href = '{{ url("deepseek/result") }}/' + id;
    }

    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
        deleteChatId = null;
    }

    function closeShareModal() {
        document.getElementById('shareModal').style.display = 'none';
        currentShareId = null;
    }

    function showNotification(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-' + type;
        alertDiv.innerHTML = '<span><i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message + '</span><i class="fas fa-times" style="cursor: pointer;" onclick="this.parentElement.remove()"></i>';
        document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const deleteModal = document.getElementById('deleteModal');
        const shareModal = document.getElementById('shareModal');
        if (event.target === deleteModal) closeModal();
        if (event.target === shareModal) closeShareModal();
    }

    // Auto-submit search on input (with debounce)
    let searchTimeout;
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 500);
        });
    }

    document.getElementById('confirmDeleteBtn')?.addEventListener('click', confirmDelete);
</script>

</body>
</html>