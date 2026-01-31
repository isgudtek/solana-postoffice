<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postoffice — Encrypted Mail</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: #fafafa;
    font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
    color: #0a0a0a;
    margin: 0;
    padding: 0;
    line-height: 1.5;
}

header {
    border-bottom: 1px solid #e5e5e5;
    background: white;
}

.header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 16px 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 16px;
    font-weight: 700;
    color: #0a0a0a;
    letter-spacing: -0.03em;
    text-transform: uppercase;
    text-decoration: none;
}

.header-links {
    display: flex;
    gap: 24px;
    align-items: center;
}

.header-link {
    color: #525252;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: color 0.15s ease;
}

.header-link:hover {
    color: #0a0a0a;
}

.inbox-container {
    max-width: 1400px;
    margin: 0 auto;
    background: white;
    min-height: 100vh;
    border-left: 1px solid #e5e5e5;
    border-right: 1px solid #e5e5e5;
}


.refresh-btn {
    background: white;
    color: #525252;
    border: 1px solid #d4d4d4;
    border-radius: 0;
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    padding: 0;
}

.refresh-btn:hover {
    background: #fafafa;
    border-color: #0a0a0a;
    color: #0a0a0a;
}

.refresh-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.refresh-btn.spinning svg {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.compose-btn {
    background: #0a0a0a;
    color: white;
    border: 1px solid #0a0a0a;
    border-radius: 0;
    padding: 10px 20px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.15s ease;
    letter-spacing: -0.01em;
}

.compose-btn:hover {
    background: #262626;
    border-color: #262626;
}

.inbox-section {
    padding-top: 0;
}

.inbox-title {
    padding: 20px 32px;
    font-size: 13px;
    font-weight: 600;
    color: #0a0a0a;
    border-bottom: 1px solid #e5e5e5;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    background: #fafafa;
}

.list-header {
    display: grid;
    grid-template-columns: 40px 200px 1fr 120px 150px;
    gap: 16px;
    padding: 12px 32px;
    background: white;
    border-bottom: 1px solid #e5e5e5;
    font-size: 11px;
    font-weight: 600;
    color: #737373;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.sortable-header {
    cursor: pointer;
    user-select: none;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color 0.15s ease;
}

.sortable-header:hover {
    color: #0a0a0a;
}

.sort-arrow {
    font-size: 10px;
    opacity: 0.4;
}

.sort-arrow.active {
    opacity: 1;
    color: #0a0a0a;
}

.message-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.message-row {
    display: grid;
    grid-template-columns: 40px 200px 1fr 120px 150px;
    gap: 16px;
    padding: 16px 32px;
    border-bottom: 1px solid #f5f5f5;
    align-items: center;
    transition: all 0.15s ease;
}

.message-row:hover {
    background: #fafafa;
    border-left: 2px solid #0a0a0a;
    padding-left: 30px;
}

.message-row:hover .message-actions {
    opacity: 1;
}

.message-content-area {
    cursor: pointer;
}

.message-actions {
    display: flex;
    gap: 6px;
    opacity: 0;
    transition: opacity 0.15s ease;
}

.action-btn {
    padding: 6px 14px;
    border: 1px solid #d4d4d4;
    background: white;
    border-radius: 0;
    font-size: 11px;
    font-weight: 500;
    cursor: pointer;
    color: #525252;
    transition: all 0.15s ease;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.action-btn:hover {
    background: #0a0a0a;
    border-color: #0a0a0a;
    color: white;
}

.action-btn-burn:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: white;
}

.message-row.unread {
    font-weight: 500;
    background: #fafafa;
}

.message-checkbox {
    width: 16px;
    height: 16px;
    cursor: pointer;
    border: 1px solid #d4d4d4;
    border-radius: 0;
}

.message-sender {
    font-size: 13px;
    color: #0a0a0a;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -0.01em;
}

.message-content {
    font-size: 13px;
    color: #737373;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.message-subject {
    color: #0a0a0a;
    font-weight: 500;
}

.message-time {
    font-size: 12px;
    color: #a3a3a3;
    text-align: right;
    font-variant-numeric: tabular-nums;
}

.wallet-section {
    background: #fafafa;
    padding: 24px 32px;
    margin: 0;
    border-top: 1px solid #e5e5e5;
    border-bottom: 1px solid #e5e5e5;
    text-align: center;
}

.btn {
    padding: 10px 20px;
    background: white;
    color: #0a0a0a;
    border: 1px solid #d4d4d4;
    border-radius: 0;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: inherit;
    letter-spacing: -0.01em;
}

.btn:hover {
    background: #0a0a0a;
    border-color: #0a0a0a;
    color: white;
}

.btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.btn-secondary {
    background: #dc2626;
    color: white;
    border-color: #dc2626;
}

.btn-secondary:hover {
    background: #b91c1c;
    border-color: #b91c1c;
}

.wallet-info {
    background: white;
    padding: 12px 16px;
    margin-top: 16px;
    font-family: 'SF Mono', 'Monaco', 'Cascadia Code', monospace;
    font-size: 12px;
    display: none;
    border-radius: 0;
    border: 1px solid #e5e5e5;
    color: #525252;
}

.wallet-info.visible {
    display: block;
}

.status-message {
    text-align: center;
    padding: 16px 32px;
    background: #fafafa;
    margin: 0;
    border-top: 1px solid #e5e5e5;
    border-bottom: 1px solid #e5e5e5;
    font-size: 13px;
    color: #0a0a0a;
    display: none;
}

.empty-state {
    text-align: center;
    padding: 80px 32px;
    color: #a3a3a3;
}

.empty-state svg {
    width: 48px;
    height: 48px;
    margin-bottom: 20px;
    stroke: #d4d4d4;
    stroke-width: 1.5;
}

.empty-state h3 {
    color: #0a0a0a;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}

.empty-state p {
    color: #737373;
    font-size: 13px;
}

.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(10, 10, 10, 0.6);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(4px);
}

.modal-overlay.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 0;
    padding: 32px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    border: 1px solid #e5e5e5;
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #0a0a0a;
    margin-bottom: 16px;
    letter-spacing: -0.02em;
}

.modal-body {
    color: #525252;
    line-height: 1.6;
    margin-bottom: 24px;
    font-size: 14px;
}

.modal-buttons {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.modal-btn {
    padding: 10px 20px;
    border: 1px solid #d4d4d4;
    border-radius: 0;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.15s ease;
    background: white;
    font-family: inherit;
    font-size: 13px;
    letter-spacing: -0.01em;
}

.modal-btn-cancel {
    color: #525252;
}

.modal-btn-cancel:hover {
    background: #fafafa;
    border-color: #0a0a0a;
}

.modal-btn-confirm {
    background: #0a0a0a;
    color: white;
    border-color: #0a0a0a;
}

.modal-btn-confirm:hover {
    background: #262626;
    border-color: #262626;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #0a0a0a;
    font-weight: 600;
    font-size: 13px;
    letter-spacing: 0.01em;
}

.form-group input {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #d4d4d4;
    border-radius: 0;
    font-family: 'SF Mono', 'Monaco', 'Cascadia Code', monospace;
    font-size: 13px;
    color: #0a0a0a;
    background: #fafafa;
    transition: all 0.15s ease;
}

.form-group input:focus {
    outline: none;
    border-color: #0a0a0a;
    background: white;
}

.brand-header {
    padding: 20px 32px;
    border-bottom: 1px solid #e5e5e5;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.brand-info {
    display: flex;
    flex-direction: column;
}

.brand-logo {
    font-size: 16px;
    font-weight: 700;
    color: #0a0a0a;
    letter-spacing: -0.03em;
    text-transform: uppercase;
}

.brand-tagline {
    font-size: 11px;
    color: #a3a3a3;
    margin-top: 2px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="index.php" class="logo">Postoffice</a>
            <nav class="header-links">
                <a href="compose.php" class="header-link">Compose</a>
                <a href="my_letters.php" class="header-link" style="color: #0a0a0a;">Inbox</a>
                <a href="pricing.php" class="header-link">Pricing</a>
            </nav>
        </div>
    </header>

    <div class="inbox-container">

        <div class="wallet-section">
            <button id="connectBtn" class="btn" onclick="connectWallet()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Connect Wallet
            </button>
            <button id="disconnectBtn" class="btn btn-secondary" onclick="disconnectWallet()" style="display: none;">
                Disconnect
            </button>
            <div id="walletInfo" class="wallet-info"></div>
        </div>

        <div id="statusMessage" class="status-message" style="display: none;"></div>

        <div class="inbox-section">
            <div class="inbox-title" style="display: flex; justify-content: space-between; align-items: center;">
                <span>Inbox (<span id="message-count">0</span>)</span>
                <button class="refresh-btn" onclick="refreshMessages()" id="refreshBtn" title="Refresh messages">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                </button>
            </div>

            <div class="list-header">
                <div></div>
                <div class="sortable-header" onclick="sortMessages('sender')">
                    <span>Sender</span>
                    <span id="sort-arrow-sender" class="sort-arrow">▼</span>
                </div>
                <div class="sortable-header" onclick="sortMessages('subject')">
                    <span>Subject</span>
                    <span id="sort-arrow-subject" class="sort-arrow">▼</span>
                </div>
                <div class="sortable-header" onclick="sortMessages('date')" style="justify-content: flex-end;">
                    <span>Date</span>
                    <span id="sort-arrow-date" class="sort-arrow active">▼</span>
                </div>
                <div style="text-align: center;">Actions</div>
            </div>

            <ul id="lettersGrid" class="message-list"></ul>
        </div>

        <div id="emptyState" class="empty-state" style="display: none;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            <h3 id="emptyStateTitle">No messages</h3>
            <p id="emptyStateText">You don't have any encrypted messages in this wallet yet.</p>
        </div>
    </div>

    <script>
        // Check for wallet parameter in URL (view-only mode)
        const urlParams = new URLSearchParams(window.location.search);
        const viewOnlyWallet = urlParams.get('wallet');

        let connectedWallet = localStorage.getItem('postoffice_wallet') || null;
        let walletPopup = null;
        let currentLetters = [];
        let currentSort = { column: 'date', direction: 'desc' };

        async function connectWallet() {
            const connectBtn = document.getElementById('connectBtn');
            const statusMessage = document.getElementById('statusMessage');
            const lettersGrid = document.getElementById('lettersGrid');
            const emptyState = document.getElementById('emptyState');

            // Clear previous state
            lettersGrid.innerHTML = '';
            emptyState.style.display = 'none';

            statusMessage.style.display = 'block';
            statusMessage.innerHTML = '<div class="loading"></div> Connecting wallet...';
            connectBtn.disabled = true;

            try {
                // Check if Phantom is installed
                if (typeof window.solana === 'undefined' || !window.solana.isPhantom) {
                    statusMessage.style.display = 'none';
                    connectBtn.disabled = false;
                    openWalletPopup();
                    return;
                }

                // Connect to Phantom (will prompt user if needed)
                const response = await window.solana.connect({ onlyIfTrusted: false });
                const publicKey = response.publicKey.toString();
                connectedWallet = publicKey;
                localStorage.setItem('postoffice_wallet', publicKey);

                document.getElementById('walletInfo').textContent = 'Connected: ' + publicKey;
                document.getElementById('walletInfo').classList.add('visible');
                document.getElementById('connectBtn').style.display = 'none';
                document.getElementById('disconnectBtn').style.display = 'inline-flex';

                await loadLetters(publicKey);

            } catch (err) {
                console.error('Connection failed:', err);
                let errorMsg = 'Connection failed';

                if (err.message && err.message.includes('User rejected')) {
                    errorMsg = 'Connection cancelled by user';
                } else if (err.code === 4001) {
                    errorMsg = 'Connection cancelled by user';
                } else if (err.message) {
                    errorMsg = err.message;
                }

                statusMessage.innerHTML = '❌ ' + errorMsg;
                connectBtn.disabled = false;
                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 5000);
            }
        }

        function openWalletPopup() {
            const width = 450;
            const height = 600;
            const left = (screen.width - width) / 2;
            const top = (screen.height - height) / 2;

            walletPopup = window.open(
                'connect_solana.html',
                'Connect Wallet',
                `width=${width},height=${height},left=${left},top=${top}`
            );
        }

        window.addEventListener('message', async (event) => {
            if (event.data.type === 'WALLET_CONNECTED') {
                const publicKey = event.data.wallet;
                connectedWallet = publicKey;
                localStorage.setItem('postoffice_wallet', publicKey);

                // Clear previous state
                document.getElementById('lettersGrid').innerHTML = '';
                document.getElementById('emptyState').style.display = 'none';

                document.getElementById('walletInfo').textContent = 'Connected: ' + publicKey;
                document.getElementById('walletInfo').classList.add('visible');
                document.getElementById('connectBtn').style.display = 'none';
                document.getElementById('connectBtn').disabled = false;
                document.getElementById('disconnectBtn').style.display = 'inline-flex';

                await loadLetters(publicKey);
            }
        });

        async function disconnectWallet() {
            const disconnectBtn = document.getElementById('disconnectBtn');
            const statusMessage = document.getElementById('statusMessage');

            disconnectBtn.disabled = true;
            disconnectBtn.textContent = 'Disconnecting...';

            // Disconnect from Phantom
            try {
                if (window.solana && window.solana.isConnected) {
                    await window.solana.disconnect();
                }
            } catch (err) {
                console.error('Disconnect error:', err);
            }

            // Reset UI state
            connectedWallet = null;
            localStorage.removeItem('postoffice_wallet');
            document.getElementById('walletInfo').textContent = '';
            document.getElementById('walletInfo').classList.remove('visible');
            document.getElementById('connectBtn').style.display = 'inline-flex';
            document.getElementById('connectBtn').disabled = false;
            document.getElementById('disconnectBtn').style.display = 'none';
            disconnectBtn.disabled = false;
            disconnectBtn.textContent = 'Disconnect';
            document.getElementById('lettersGrid').innerHTML = '';
            document.getElementById('emptyState').style.display = 'none';

            statusMessage.style.display = 'block';
            statusMessage.innerHTML = '✅ Wallet disconnected. Connect a different wallet to view letters.';

            setTimeout(() => {
                statusMessage.style.display = 'none';
            }, 3000);
        }

        async function refreshMessages() {
            if (!connectedWallet) {
                const statusMessage = document.getElementById('statusMessage');
                statusMessage.style.display = 'block';
                statusMessage.innerHTML = '⚠️ Please connect your wallet first';
                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 3000);
                return;
            }

            const refreshBtn = document.getElementById('refreshBtn');
            refreshBtn.disabled = true;
            refreshBtn.classList.add('spinning');

            await loadLetters(connectedWallet);

            refreshBtn.disabled = false;
            refreshBtn.classList.remove('spinning');
        }

        async function loadLetters(walletAddress) {
            const statusMessage = document.getElementById('statusMessage');
            const lettersGrid = document.getElementById('lettersGrid');
            const emptyState = document.getElementById('emptyState');

            statusMessage.style.display = 'block';
            statusMessage.innerHTML = '<div class="loading"></div> Loading sealed letters...';
            lettersGrid.innerHTML = '';
            emptyState.style.display = 'none';

            try {
                // Single server-side API call
                const apiUrl = '/notary/api.php?action=get_wallet_sealed_letters';
                console.log('Fetching sealed letters for wallet:', walletAddress);
                console.log('API URL:', apiUrl);

                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ wallet: walletAddress })
                });

                console.log('Response status:', response.status);
                console.log('Response URL:', response.url);
                console.log('Response headers:', [...response.headers.entries()]);

                if (!response.ok) {
                    const text = await response.text();
                    console.error('HTTP Error:', response.status);
                    console.error('Response text:', text.substring(0, 500));
                    statusMessage.innerHTML = '❌ HTTP ' + response.status + ': Server error loading letters';
                    return;
                }

                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);

                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text.substring(0, 500));
                    statusMessage.innerHTML = '❌ Server returned non-JSON response (check console)';
                    return;
                }

                const data = await response.json();
                console.log('API response:', data);

                if (!data.success) {
                    statusMessage.innerHTML = '❌ Error: ' + (data.error || 'Failed to load');
                    return;
                }

                const letters = data.letters || [];

                statusMessage.style.display = 'none';

                if (letters.length === 0) {
                    emptyState.style.display = 'block';

                    if (data.total_assets_checked > 0) {
                        document.getElementById('emptyStateTitle').textContent = 'No encrypted messages';
                        document.getElementById('emptyStateText').innerHTML =
                            `Found ${data.total_assets_checked} NFT${data.total_assets_checked === 1 ? '' : 's'} in your wallet, but none are encrypted messages.`;
                    } else {
                        document.getElementById('emptyStateTitle').textContent = 'No messages';
                        document.getElementById('emptyStateText').textContent = 'This wallet has no messages yet.';
                    }
                    return;
                }

                // Store letters globally for sorting
                currentLetters = letters;

                // Update message count
                document.getElementById('message-count').textContent = letters.length;

                // Apply current sort and render
                applySortAndRender();

            } catch (error) {
                console.error('Error loading letters:', error);
                statusMessage.style.display = 'block';
                statusMessage.innerHTML = '❌ Error: ' + error.message;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function sortMessages(column) {
            // Toggle direction if clicking same column, otherwise default to desc
            if (currentSort.column === column) {
                currentSort.direction = currentSort.direction === 'desc' ? 'asc' : 'desc';
            } else {
                currentSort.column = column;
                currentSort.direction = column === 'date' ? 'desc' : 'asc';
            }

            applySortAndRender();
        }

        function applySortAndRender() {
            const lettersGrid = document.getElementById('lettersGrid');

            // Sort the letters
            const sorted = [...currentLetters].sort((a, b) => {
                let valA, valB;

                switch (currentSort.column) {
                    case 'sender':
                        valA = (a.sender_email || a.sender_wallet || 'Unknown').toLowerCase();
                        valB = (b.sender_email || b.sender_wallet || 'Unknown').toLowerCase();
                        break;
                    case 'subject':
                        valA = (a.name || '').toLowerCase();
                        valB = (b.name || '').toLowerCase();
                        break;
                    case 'date':
                        valA = new Date(a.created_at || 0).getTime();
                        valB = new Date(b.created_at || 0).getTime();
                        break;
                    default:
                        return 0;
                }

                if (valA < valB) return currentSort.direction === 'asc' ? -1 : 1;
                if (valA > valB) return currentSort.direction === 'asc' ? 1 : -1;
                return 0;
            });

            // Update sort arrows
            ['sender', 'subject', 'date'].forEach(col => {
                const arrow = document.getElementById(`sort-arrow-${col}`);
                if (col === currentSort.column) {
                    arrow.classList.add('active');
                    arrow.textContent = currentSort.direction === 'desc' ? '▼' : '▲';
                } else {
                    arrow.classList.remove('active');
                    arrow.textContent = '▼';
                }
            });

            // Render message rows
            lettersGrid.innerHTML = sorted.map(letter => {
                const senderDisplay = letter.sender_email ||
                                    (letter.sender_wallet ? letter.sender_wallet.substring(0, 16) + '...' : 'Unknown');

                const dateDisplay = formatMessageTime(letter.created_at);
                const viewUrl = `view.php?slug=${escapeHtml(letter.slug)}`;

                return `
                    <li class="message-row unread" data-asset-id="${escapeHtml(letter.asset_id)}">
                        <input type="checkbox" class="message-checkbox" onclick="event.stopPropagation()" />
                        <div class="message-sender message-content-area" onclick="window.location.href='${viewUrl}'">${escapeHtml(senderDisplay)}</div>
                        <div class="message-content message-content-area" onclick="window.location.href='${viewUrl}'">
                            <span class="message-subject">${escapeHtml(letter.name)}</span>
                            <span style="color: #999;"> - Encrypted message</span>
                        </div>
                        <div class="message-time message-content-area" onclick="window.location.href='${viewUrl}'">${dateDisplay}</div>
                        <div class="message-actions">
                            <button class="action-btn" onclick="event.stopPropagation(); sendSealedLetter('${escapeHtml(letter.asset_id)}', '${escapeHtml(letter.slug)}', '${escapeHtml(letter.name)}')">Send</button>
                            <button class="action-btn action-btn-burn" onclick="event.stopPropagation(); burnSealedLetter('${escapeHtml(letter.asset_id)}', '${escapeHtml(letter.slug)}', '${escapeHtml(letter.name)}')">Burn</button>
                        </div>
                    </li>
                `;
            }).join('');
        }

        function formatMessageTime(timestamp) {
            if (!timestamp) return 'Unknown';

            const date = new Date(timestamp + ' GMT');
            const now = new Date();
            const diffMs = now - date;
            const diffHours = diffMs / (1000 * 60 * 60);

            if (diffHours < 24) {
                return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
            } else if (diffHours < 48) {
                return 'Yesterday';
            } else if (diffHours < 168) {
                return date.toLocaleDateString('en-US', { weekday: 'short' });
            } else {
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            }
        }

        // Modal helper functions
        function showModal(title, bodyHtml, buttons) {
            const overlay = document.getElementById('modalOverlay');
            const titleEl = document.getElementById('modalTitle');
            const bodyEl = document.getElementById('modalBody');
            const buttonsEl = document.getElementById('modalButtons');

            titleEl.textContent = title;
            bodyEl.innerHTML = bodyHtml;
            buttonsEl.innerHTML = '';

            buttons.forEach(btn => {
                const button = document.createElement('button');
                button.className = btn.className || 'modal-btn';
                button.textContent = btn.text;
                button.onclick = () => {
                    closeModal();
                    if (btn.callback) btn.callback();
                };
                buttonsEl.appendChild(button);
            });

            overlay.classList.add('active');

            // Close on ESC key
            const escHandler = (e) => {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', escHandler);
                }
            };
            document.addEventListener('keydown', escHandler);

            // Close on click outside
            overlay.onclick = (e) => {
                if (e.target === overlay) {
                    closeModal();
                }
            };
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.remove('active');
        }

        function modalAlert(message, title = 'Notice') {
            showModal(title, message, [
                { text: 'OK', className: 'modal-btn modal-btn-confirm', callback: null }
            ]);
        }

        function modalConfirm(message, onConfirm, title = 'Confirm', confirmText = 'Confirm') {
            showModal(title, message, [
                { text: 'Cancel', className: 'modal-btn modal-btn-cancel', callback: null },
                { text: confirmText, className: 'modal-btn modal-btn-confirm', callback: onConfirm }
            ]);
        }

        // Check wallet balance and auto-fund if needed
        async function checkAndFundWallet() {
            const statusMessage = document.getElementById('statusMessage');

            try {
                statusMessage.style.display = 'block';
                statusMessage.innerHTML = '⏳ Checking wallet balance...';

                const response = await fetch('check_and_fund.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ wallet: connectedWallet })
                });

                const data = await response.json();

                if (data.success) {
                    if (data.funded) {
                        statusMessage.innerHTML = `✅ ${data.message}`;
                        setTimeout(() => {
                            statusMessage.style.display = 'none';
                        }, 3000);
                    } else {
                        statusMessage.style.display = 'none';
                    }
                    return true;
                } else {
                    statusMessage.innerHTML = `⚠️ ${data.error || 'Could not check balance'}`;
                    if (data.error && data.error.includes('faucet')) {
                        // Show faucet link
                        setTimeout(() => {
                            statusMessage.innerHTML += '<br><a href="https://faucet.solana.com" target="_blank" style="color: #4285f4; text-decoration: underline;">Get devnet SOL from faucet →</a>';
                        }, 1000);
                    }
                    return false;
                }
            } catch (error) {
                console.error('Balance check error:', error);
                statusMessage.innerHTML = '⚠️ Could not check balance. Continuing anyway...';
                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 2000);
                return true; // Continue anyway if check fails
            }
        }

        // Send sealed letter function
        async function sendSealedLetter(assetId, slug, name) {
            if (!connectedWallet || typeof window.solana === 'undefined') {
                modalAlert('Please connect your wallet first.', 'Wallet Required');
                return;
            }

            // Show modal to get recipient address
            const recipientHtml = `
                <div style="margin-bottom: 1rem;">
                    <p>Send <strong>${escapeHtml(name)}</strong> to another wallet.</p>
                    <p style="font-size: 0.875rem; color: #718096; margin-top: 0.5rem;">
                        The recipient will own this sealed letter and be able to decrypt it.
                    </p>
                </div>
                <div class="form-group">
                    <label>Recipient Wallet Address</label>
                    <input type="text" id="sendRecipientAddress" placeholder="Solana wallet address..."
                           style="width: 100%; padding: 0.75rem; background: white; color: #1a202c;
                                  border: 2px solid #e2e8f0; border-radius: 8px; font-family: monospace;">
                </div>
                <p style="font-size: 0.8rem; opacity: 0.7; margin-top: 0.5rem; color: #f56565;">
                    ⚠️ This action is irreversible. The NFT will be transferred permanently.
                </p>
            `;

            modalConfirm(recipientHtml, async () => {
                const recipient = document.getElementById('sendRecipientAddress')?.value?.trim();

                if (!recipient) {
                    modalAlert('Please enter a recipient wallet address.', 'Error');
                    return;
                }

                // Validate Solana address format (32-44 characters, base58)
                if (recipient.length < 32 || recipient.length > 44) {
                    modalAlert('Invalid Solana wallet address format.', 'Error');
                    return;
                }

                await executeSendSealedLetter(assetId, slug, name, recipient);
            }, 'Send Sealed Letter', 'Send');
        }

        async function executeSendSealedLetter(assetId, slug, name, recipient) {
            try {
                // Check balance and fund if needed
                const canProceed = await checkAndFundWallet();
                if (!canProceed) {
                    return;
                }

                // Show status
                const statusMessage = document.getElementById('statusMessage');
                statusMessage.style.display = 'block';
                statusMessage.innerHTML = '⏳ Building transfer transaction...';

                // Call backend to build transaction
                const response = await fetch('/notary/api.php?action=buildTransferTx', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        assetId: assetId,
                        wallet: connectedWallet,
                        recipient: recipient
                    })
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('HTTP Error:', response.status, text.substring(0, 200));
                    throw new Error('HTTP ' + response.status + ': ' + (text.substring(0, 100) || 'Server error'));
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response from buildTransferTx:', text.substring(0, 500));
                    throw new Error('Server returned non-JSON response. Check console for details.');
                }

                const data = await response.json();

                if (!data.success) {
                    statusMessage.innerHTML = '❌ Failed to build transaction: ' + (data.error || 'Unknown error');
                    return;
                }

                statusMessage.innerHTML = '🔐 Please approve the transaction in your wallet...';

                // Decode transaction and add fresh blockhash
                const transactionBuffer = Uint8Array.from(atob(data.transaction), c => c.charCodeAt(0));
                const transaction = solanaWeb3.Transaction.from(transactionBuffer);

                // Load RPC URL from config
                const configResp = await fetch('/nft/config.json');
                const config = await configResp.json();
                const connection = new solanaWeb3.Connection(config.rpc_url, 'confirmed');

                // Get fresh blockhash before signing
                const { blockhash, lastValidBlockHeight } = await connection.getLatestBlockhash();
                transaction.recentBlockhash = blockhash;

                // Sign with Phantom
                const signedTx = await window.solana.signTransaction(transaction);

                statusMessage.innerHTML = '📡 Sending transaction...';

                // Send transaction
                const signature = await connection.sendRawTransaction(signedTx.serialize());

                statusMessage.innerHTML = '⏳ Confirming transfer...';

                // Wait for confirmation
                await connection.confirmTransaction({ signature, blockhash, lastValidBlockHeight }, 'confirmed');

                statusMessage.innerHTML = '✅ Transfer successful!';

                // Show success modal with explorer link
                modalAlert(
                    `Sealed letter transferred successfully!<br><br>
                    <strong>${escapeHtml(name)}</strong> has been sent to:<br>
                    <code style="font-size: 0.75rem; word-break: break-all;">${escapeHtml(recipient)}</code><br><br>
                    <a href="https://solscan.io/tx/${signature}?cluster=devnet" target="_blank"
                       style="color: #667eea; text-decoration: underline;">View on Solscan</a>`,
                    'Transfer Complete'
                );

                // Refresh the list after a short delay to allow blockchain indexer to update
                setTimeout(async () => {
                    await refreshMessages();
                    statusMessage.style.display = 'none';
                }, 2000);

            } catch (error) {
                console.error('Transfer error:', error);
                const statusMessage = document.getElementById('statusMessage');
                statusMessage.style.display = 'block';
                statusMessage.innerHTML = '❌ Error: ' + error.message;
            }
        }

        // Burn sealed letter function
        async function burnSealedLetter(assetId, slug, name) {
            if (!connectedWallet || typeof window.solana === 'undefined') {
                modalAlert('Please connect your wallet first.', 'Wallet Required');
                return;
            }

            const burnHtml = `
                <div style="color: #f56565; margin-bottom: 1rem; font-weight: bold;">
                    ⚠️ WARNING: PERMANENT DESTRUCTION
                </div>
                <p>You are about to <strong>permanently destroy</strong> this sealed letter:</p>
                <p style="margin: 1rem 0; font-size: 1.2rem; font-weight: 600;">${escapeHtml(name)}</p>
                <p style="font-size: 0.875rem; color: #718096;">
                    The NFT will be permanently burned and removed from your wallet.
                    The encrypted content will remain on IPFS but become inaccessible.
                </p>
                <p style="font-size: 0.875rem; color: #f56565; margin-top: 1rem;">
                    This action cannot be undone.
                </p>
            `;

            modalConfirm(burnHtml, async () => {
                await executeBurnSealedLetter(assetId, slug, name);
            }, 'Burn Sealed Letter', 'Burn Forever');
        }

        async function executeBurnSealedLetter(assetId, slug, name) {
            try {
                // Check balance and fund if needed
                const canProceed = await checkAndFundWallet();
                if (!canProceed) {
                    return;
                }

                const statusMessage = document.getElementById('statusMessage');
                statusMessage.style.display = 'block';
                statusMessage.innerHTML = '⏳ Building burn transaction...';

                // Call backend to build burn transaction
                const response = await fetch('/notary/api.php?action=buildBurnTx', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        assetId: assetId,
                        wallet: connectedWallet
                    })
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('HTTP Error:', response.status, text.substring(0, 200));
                    throw new Error('HTTP ' + response.status + ': ' + (text.substring(0, 100) || 'Server error'));
                }

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response from buildBurnTx:', text.substring(0, 500));
                    throw new Error('Server returned non-JSON response. Check console for details.');
                }

                const data = await response.json();

                if (!data.success) {
                    statusMessage.innerHTML = '❌ Failed to build transaction: ' + (data.error || 'Unknown error');
                    return;
                }

                statusMessage.innerHTML = '🔐 Please approve the burn transaction in your wallet...';

                // Decode transaction and add fresh blockhash
                const transactionBuffer = Uint8Array.from(atob(data.transaction), c => c.charCodeAt(0));
                const transaction = solanaWeb3.Transaction.from(transactionBuffer);

                // Load RPC URL from config
                const configResp = await fetch('/nft/config.json');
                const config = await configResp.json();
                const connection = new solanaWeb3.Connection(config.rpc_url, 'confirmed');

                // Get fresh blockhash before signing
                const { blockhash, lastValidBlockHeight } = await connection.getLatestBlockhash();
                transaction.recentBlockhash = blockhash;

                // Sign with Phantom
                const signedTx = await window.solana.signTransaction(transaction);

                statusMessage.innerHTML = '🔥 Burning NFT...';

                // Send transaction
                const signature = await connection.sendRawTransaction(signedTx.serialize());

                statusMessage.innerHTML = '⏳ Confirming burn...';

                // Wait for confirmation
                await connection.confirmTransaction({ signature, blockhash, lastValidBlockHeight }, 'confirmed');

                // Optional: Update database to mark letter as burned
                try {
                    await fetch('/notary/api.php?action=cleanup_burned_letter', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            slug: slug,
                            wallet: connectedWallet,
                            burnSignature: signature
                        })
                    });
                } catch (e) {
                    console.error('Database cleanup failed (non-critical):', e);
                }

                statusMessage.innerHTML = '✅ Sealed letter burned successfully!';

                // Show success modal
                modalAlert(
                    `Sealed letter destroyed successfully!<br><br>
                    <strong>${escapeHtml(name)}</strong> has been permanently burned.<br><br>
                    <a href="https://solscan.io/tx/${signature}?cluster=devnet" target="_blank"
                       style="color: #667eea; text-decoration: underline;">View on Solscan</a>`,
                    'Burn Complete'
                );

                // Refresh the list after a short delay to allow blockchain indexer to update
                setTimeout(async () => {
                    await refreshMessages();
                    statusMessage.style.display = 'none';
                }, 2000);

            } catch (error) {
                console.error('Burn error:', error);
                const statusMessage = document.getElementById('statusMessage');
                statusMessage.style.display = 'block';
                statusMessage.innerHTML = '❌ Error: ' + error.message;
            }
        }

        // Auto-connect on load if Phantom is already connected
        window.addEventListener('load', async () => {
            // Check for view-only mode first
            if (viewOnlyWallet) {
                console.log('View-only mode for wallet:', viewOnlyWallet);
                document.getElementById('statusMessage').style.display = 'block';
                document.getElementById('statusMessage').innerHTML = '👁️ Viewing inbox for: ' + viewOnlyWallet.substring(0, 8) + '...' + viewOnlyWallet.substring(viewOnlyWallet.length - 4) + ' (read-only mode)';
                document.getElementById('walletInfo').textContent = 'Viewing: ' + viewOnlyWallet;
                document.getElementById('walletInfo').classList.add('visible');
                await loadLetters(viewOnlyWallet);
                return;
            }

            // Normal auto-connect flow
            try {
                if (typeof window.solana !== 'undefined' && window.solana.isPhantom) {
                    // Try to connect silently if already trusted
                    const response = await window.solana.connect({ onlyIfTrusted: true });
                    if (response && response.publicKey) {
                        const publicKey = response.publicKey.toString();
                        connectedWallet = publicKey;
                        localStorage.setItem('postoffice_wallet', publicKey);

                        document.getElementById('walletInfo').textContent = 'Connected: ' + publicKey;
                        document.getElementById('walletInfo').classList.add('visible');
                        document.getElementById('connectBtn').style.display = 'none';
                        document.getElementById('disconnectBtn').style.display = 'inline-flex';

                        await loadLetters(publicKey);
                    }
                }
            } catch (err) {
                // Silent fail on auto-connect - user can manually connect
                console.log('Auto-connect not available:', err);
            }
        });
    </script>

    <!-- Modal for confirmations -->
    <div id="modalOverlay" class="modal-overlay">
        <div class="modal-content">
            <div id="modalTitle" class="modal-title"></div>
            <div id="modalBody" class="modal-body"></div>
            <div id="modalButtons" class="modal-buttons"></div>
        </div>
    </div>

    <!-- Load Solana Web3.js for transaction handling -->
    <script src="https://unpkg.com/@solana/web3.js@latest/lib/index.iife.js"></script>
</body>
</html>
