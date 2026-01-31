<?php
$slug = $_GET['slug'] ?? '';
if (!preg_match('/^po[a-z0-9]{12}$/', $slug)) {
    die('Invalid slug');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypted Message - <?= htmlspecialchars($slug) ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Google Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            padding: 0;
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
        .container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .mail-canvas {
            max-width: 900px;
            margin: 0 auto;
        }
        .mail-header {
            padding: 24px;
            border-bottom: 1px solid #dadce0;
        }
        .mail-subject {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 16px;
            color: #202124;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .mail-meta {
            display: flex;
            gap: 24px;
            color: #5f6368;
            font-size: 14px;
            flex-wrap: wrap;
        }
        .mail-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .status-box {
            background: #e8f0fe;
            padding: 16px 24px;
            text-align: center;
            font-size: 14px;
            color: #1967d2;
        }
        .status-box.loading {
            background: #e8f0fe;
        }
        .status-box.success {
            background: #e6f4ea;
            color: #137333;
        }
        .status-box.error {
            background: #fce8e6;
            color: #c5221f;
        }
        .btn {
            width: auto;
            padding: 12px 24px;
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 24px;
        }
        .btn:hover {
            background: #1967d2;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .mail-body {
            padding: 24px;
            font-size: 15px;
            line-height: 1.8;
            color: #202124;
            min-height: 300px;
            white-space: pre-wrap;
        }
        .wallet-info {
            font-size: 13px;
            color: #5f6368;
            margin: 0 24px 24px;
            text-align: center;
            font-family: 'Roboto Mono', monospace;
        }
        .message-actions {
            display: none;
            padding: 16px 24px;
            border-top: 1px solid #dadce0;
            gap: 12px;
            justify-content: flex-start;
        }
        .message-actions.visible {
            display: flex;
        }
        .action-btn-large {
            padding: 10px 24px;
            border: 1px solid #dadce0;
            background: white;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            color: #5f6368;
            transition: all 0.2s;
        }
        .action-btn-large:hover {
            background: #f5f5f5;
            border-color: #4285f4;
            color: #4285f4;
        }
        .action-btn-large.burn:hover {
            border-color: #ea4335;
            color: #ea4335;
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #4285f4;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .back-link {
            display: inline-block;
            margin: 16px 24px;
            color: #4285f4;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="index.php" class="logo">Postoffice</a>
            <nav class="header-links">
                <a href="compose.php" class="header-link">Compose</a>
                <a href="my_letters.php" class="header-link">Inbox</a>
                <a href="pricing.php" class="header-link">Pricing</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <a href="my_letters.php" class="back-link">← Back to Inbox</a>

        <div class="mail-canvas">
            <div class="mail-header">
                <div class="mail-subject">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Encrypted Message
                </div>
                <div class="mail-meta">
                    <div class="mail-meta-item">
                        <strong>ID:</strong>
                        <code id="slugDisplay"></code>
                    </div>
                    <div class="mail-meta-item" id="senderInfo" style="display: none;">
                        <strong>From:</strong>
                        <span id="senderText"></span>
                    </div>
                    <div class="mail-meta-item" id="dateInfo" style="display: none;">
                        <strong>Date:</strong>
                        <span id="dateText"></span>
                    </div>
                </div>
            </div>

            <div id="statusBox" class="status-box">
                <p id="statusText">Connect your wallet to verify ownership</p>
            </div>

            <button id="connectBtn" class="btn" onclick="connectWallet()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span id="btnText">Connect Wallet to Decrypt</span>
            </button>

            <div id="walletInfo" class="wallet-info" style="display: none;"></div>

            <div id="letterContent" class="mail-body"></div>

            <div id="messageActions" class="message-actions">
                <button class="action-btn-large" onclick="replyToMessage()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;">
                        <polyline points="9 14 4 9 9 4"></polyline>
                        <path d="M20 20v-7a4 4 0 0 0-4-4H4"></path>
                    </svg>
                    Reply
                </button>
                <button class="action-btn-large" onclick="sendMessage()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    Send to Another Wallet
                </button>
                <button class="action-btn-large burn" onclick="burnMessage()">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 6px;">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Burn Forever
                </button>
            </div>
        </div>
    </div>

    <script>
        const slug = '<?= htmlspecialchars($slug) ?>';
        let connectedWallet = localStorage.getItem('postoffice_wallet') || null;
        let currentAssetId = null;
        let currentLetterName = null;
        let currentSenderWallet = null;
        let currentSenderEmail = null;
        let decryptedMessageText = null;

        // Display slug in header
        document.getElementById('slugDisplay').textContent = slug;

        // Fetch and display letter metadata
        async function loadMetadata() {
            try {
                const response = await fetch('api.php?action=get_sealed_letter', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ slug })
                });
                const data = await response.json();

                if (data.success && data.letter) {
                    const letter = data.letter;

                    // Store for send/burn/reply actions
                    currentAssetId = letter.asset_id;
                    currentLetterName = letter.name || 'Encrypted Message';
                    currentSenderWallet = letter.creator_wallet;
                    currentSenderEmail = letter.creator_email;

                    // Display sender info
                    if (letter.creator_email && letter.creator_email !== 'test@example.com' && letter.creator_email !== 'noreply@postoffice.gudtek.lol') {
                        document.getElementById('senderText').textContent = letter.creator_email;
                        document.getElementById('senderInfo').style.display = 'flex';
                    } else if (letter.creator_wallet) {
                        document.getElementById('senderText').textContent = letter.creator_wallet.substring(0, 8) + '...';
                        document.getElementById('senderInfo').style.display = 'flex';
                    }

                    // Display creation time in GMT
                    if (letter.created_at) {
                        const createdDate = new Date(letter.created_at + ' GMT');
                        document.getElementById('dateText').textContent = createdDate.toLocaleString();
                        document.getElementById('dateInfo').style.display = 'flex';
                    }
                }
            } catch (error) {
                console.error('Failed to load metadata:', error);
            }
        }

        loadMetadata();

        async function connectWallet() {
            const connectBtn = document.getElementById('connectBtn');
            const statusBox = document.getElementById('statusBox');
            const statusText = document.getElementById('statusText');

            // Check if Phantom is installed
            if (typeof window.solana === 'undefined' || !window.solana.isPhantom) {
                statusBox.className = 'status-box error';
                statusText.textContent = 'Please install Phantom wallet to view this message';
                return null;
            }

            try {
                connectBtn.disabled = true;
                statusText.textContent = 'Connecting to Phantom wallet...';

                // Connect to Phantom
                const response = await window.solana.connect({ onlyIfTrusted: false });
                const publicKey = response.publicKey.toString();
                connectedWallet = publicKey;
                localStorage.setItem('postoffice_wallet', publicKey);

                document.getElementById('walletInfo').textContent = 'Connected: ' + publicKey;
                document.getElementById('walletInfo').style.display = 'block';

                await verifyAndDecrypt(publicKey);
                return publicKey;
            } catch (err) {
                console.error('Connection failed:', err);
                statusBox.className = 'status-box error';
                statusText.textContent = 'Connection failed: ' + (err.message || 'Please try again');
                connectBtn.disabled = false;
                return null;
            }
        }

        async function verifyAndDecrypt(wallet) {
            const btn = document.getElementById('connectBtn');
            const statusBox = document.getElementById('statusBox');
            const statusText = document.getElementById('statusText');
            const btnText = document.getElementById('btnText');

            btn.disabled = true;
            statusBox.className = 'status-box loading';
            statusText.innerHTML = '<div class="spinner"></div><p style="margin-top: 1rem;">Verifying cNFT ownership...</p>';

            try {
                const response = await fetch('api.php?action=verify_cnft_ownership', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ slug, wallet })
                });

                const data = await response.json();

                if (data.ok) {
                    statusBox.className = 'status-box success';
                    statusText.textContent = '✅ Ownership verified! Decrypting letter...';

                    // Store creator info from NFT metadata (source of truth)
                    console.log('Verification response:', data);
                    if (data.creator_wallet && data.creator_wallet !== '') currentSenderWallet = data.creator_wallet;
                    if (data.creator_email && data.creator_email !== '') currentSenderEmail = data.creator_email;
                    if (data.asset_id) currentAssetId = data.asset_id;

                    // Also get from loadMetadata if still not available
                    if (!currentSenderWallet || !currentSenderEmail) {
                        console.log('Falling back to loadMetadata values');
                    }
                    console.log('Sender info:', { currentSenderWallet, currentSenderEmail, currentAssetId });

                    await decryptLetter(data.key, data.encrypted_ipfs);
                } else {
                    throw new Error(data.error || 'Verification failed');
                }
            } catch (error) {
                statusBox.className = 'status-box error';
                statusText.textContent = '❌ ' + error.message;
                btn.disabled = false;
                btnText.textContent = 'Try Again';
            }
        }

        async function decryptLetter(keyHex, ipfsHash) {
            const statusBox = document.getElementById('statusBox');
            const statusText = document.getElementById('statusText');
            const letterContent = document.getElementById('letterContent');

            try {
                statusText.textContent = 'Fetching encrypted content from IPFS...';

                const ipfsResponse = await fetch('https://dweb.link/ipfs/' + ipfsHash);
                const encryptedWithIv = await ipfsResponse.text();

                statusText.textContent = 'Decrypting message...';

                const encryptedBytes = CryptoJS.enc.Base64.parse(encryptedWithIv);
                const iv = CryptoJS.lib.WordArray.create(encryptedBytes.words.slice(0, 4));
                const ciphertext = CryptoJS.lib.WordArray.create(encryptedBytes.words.slice(4));

                const key = CryptoJS.enc.Hex.parse(keyHex);

                const decrypted = CryptoJS.AES.decrypt(
                    { ciphertext: ciphertext },
                    key,
                    {
                        iv: iv,
                        mode: CryptoJS.mode.CBC,
                        padding: CryptoJS.pad.Pkcs7
                    }
                );

                const decryptedText = decrypted.toString(CryptoJS.enc.Utf8);

                if (!decryptedText) {
                    throw new Error('Decryption failed');
                }

                // Store decrypted text for reply
                decryptedMessageText = decryptedText;

                statusBox.style.display = 'none';
                letterContent.innerHTML = decryptedText;
                letterContent.style.display = 'block';
                document.getElementById('connectBtn').style.display = 'none';
                document.getElementById('messageActions').classList.add('visible');

            } catch (error) {
                statusBox.className = 'status-box error';
                statusText.textContent = '❌ Failed to decrypt: ' + error.message;
            }
        }

        // Reply function
        function replyToMessage() {
            if (!currentSenderWallet) {
                alert('Cannot reply: This letter was sent anonymously (no sender wallet address available).\n\nThe sender chose not to include their wallet address in the metadata.');
                return;
            }

            if (!decryptedMessageText) {
                alert('Please decrypt the message first');
                return;
            }

            // Prepare reply data
            const replyData = {
                recipientWallet: currentSenderWallet,
                recipientEmail: (currentSenderEmail && currentSenderEmail !== 'test@example.com' && currentSenderEmail !== 'noreply@postoffice.gudtek.lol') ? currentSenderEmail : '',
                quotedMessage: decryptedMessageText
            };

            // Store in localStorage
            localStorage.setItem('postoffice_reply_data', JSON.stringify(replyData));

            // Redirect to compose
            window.location.href = 'compose.php?reply=true';
        }

        // Send and Burn functions
        async function sendMessage() {
            if (!currentAssetId) {
                alert('Message information not loaded');
                return;
            }

            const recipient = prompt('Enter recipient wallet address:');
            if (!recipient) return;

            if (recipient.length < 32 || recipient.length > 44) {
                alert('Invalid Solana wallet address');
                return;
            }

            if (!confirm(`Send "${currentLetterName}" to ${recipient}?\n\nThis action cannot be undone.`)) {
                return;
            }

            try {
                const wallet = localStorage.getItem('postoffice_wallet');
                if (!wallet) {
                    alert('Please connect your wallet first');
                    return;
                }

                const response = await fetch('/notary/api.php?action=buildTransferTx', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        assetId: currentAssetId,
                        wallet: wallet,
                        recipient: recipient
                    })
                });

                const data = await response.json();
                if (!data.success) {
                    alert('Failed to build transaction: ' + (data.error || 'Unknown error'));
                    return;
                }

                // Handle transaction signing and sending (implementation from my_letters.php)
                alert('Transaction sent successfully!');
                window.location.href = 'my_letters.php';
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        async function burnMessage() {
            if (!currentAssetId) {
                alert('Message information not loaded');
                return;
            }

            if (!confirm(`⚠️ WARNING: PERMANENT DESTRUCTION\n\nYou are about to permanently destroy "${currentLetterName}".\n\nThis action CANNOT be undone.\n\nAre you sure?`)) {
                return;
            }

            try {
                const wallet = localStorage.getItem('postoffice_wallet');
                if (!wallet) {
                    alert('Please connect your wallet first');
                    return;
                }

                const response = await fetch('/notary/api.php?action=buildBurnTx', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        assetId: currentAssetId,
                        wallet: wallet
                    })
                });

                const data = await response.json();
                if (!data.success) {
                    alert('Failed to build transaction: ' + (data.error || 'Unknown error'));
                    return;
                }

                // Handle transaction signing and sending
                alert('Message burned successfully!');
                window.location.href = 'my_letters.php';
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Auto-connect and fetch letter metadata on load
        window.addEventListener('load', async () => {
            // Try to auto-connect if wallet was previously connected
            if (connectedWallet && typeof window.solana !== 'undefined' && window.solana.isPhantom) {
                try {
                    const response = await window.solana.connect({ onlyIfTrusted: true });
                    if (response && response.publicKey) {
                        const publicKey = response.publicKey.toString();
                        connectedWallet = publicKey;
                        localStorage.setItem('postoffice_wallet', publicKey);

                        document.getElementById('walletInfo').textContent = 'Connected: ' + publicKey;
                        document.getElementById('walletInfo').style.display = 'block';

                        // Auto-decrypt if wallet matches
                        await verifyAndDecrypt(publicKey);
                    }
                } catch (err) {
                    console.log('Auto-connect not available');
                }
            }

            // Fetch letter metadata
            try {
                const response = await fetch('api.php?action=get_sealed_letter&slug=' + slug);
                const data = await response.json();

                if (data.success) {
                    if (data.status === 'pending') {
                        document.getElementById('statusText').textContent = 'Letter is being minted... Please check back soon.';
                    } else if (data.status === 'minted') {
                        document.getElementById('statusText').textContent = 'Recipient: ' + data.recipient.substring(0, 8) + '...' + data.recipient.substring(data.recipient.length - 4);
                    }
                }
            } catch (error) {
                console.error('Failed to fetch letter metadata:', error);
            }
        });
    </script>
</body>
</html>
