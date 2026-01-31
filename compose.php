<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compose Message - POSTOFFICE</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f5f5;
            font-family: 'Google Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #202124;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 16px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 16px;
            color: #4285f4;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .compose-container {
            background: white;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .compose-header {
            font-size: 24px;
            font-weight: 500;
            color: #202124;
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #202124;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #dadce0;
            border-radius: 8px;
            font-family: -apple-system, sans-serif;
            font-size: 14px;
            color: #202124;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4285f4;
            box-shadow: 0 0 0 2px rgba(66,133,244,0.1);
        }

        .form-group textarea {
            min-height: 200px;
            resize: vertical;
            font-family: inherit;
        }

        .helper-text {
            font-size: 12px;
            color: #5f6368;
            margin-top: 6px;
        }

        .btn-send {
            background: #4285f4;
            color: white;
            border: none;
            border-radius: 24px;
            padding: 12px 32px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            font-family: inherit;
            flex: 1;
        }

        .btn-send:hover:not(:disabled) {
            background: #1967d2;
        }

        .btn-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-test {
            background: #34a853;
            color: white;
            border: none;
            border-radius: 24px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            font-family: inherit;
        }

        .btn-test:hover:not(:disabled) {
            background: #2d8e47;
        }

        .btn-test:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .status-message {
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            display: none;
        }

        .status-message.success {
            background: #e6f4ea;
            color: #137333;
        }

        .status-message.error {
            background: #fce8e6;
            color: #c5221f;
        }

        .status-message.info {
            background: #e8f0fe;
            color: #1967d2;
        }

        .success-panel {
            display: none;
            background: white;
            border-radius: 16px;
            padding: 48px 32px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .success-panel h2 {
            color: #137333;
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .success-panel p {
            color: #5f6368;
            margin-bottom: 24px;
        }

        .success-panel .link {
            display: inline-block;
            margin: 8px;
            padding: 10px 24px;
            border-radius: 24px;
            color: #4285f4;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .success-panel .link:hover {
            background: #e8f0fe;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="my_letters.php" class="back-link">← Back to Inbox</a>

        <div class="compose-container">
            <h1 class="compose-header">New Message</h1>

            <div id="statusMessage" class="status-message"></div>

            <div id="formPanel">
                <form id="messageForm">
                    <div class="form-group">
                        <label>From Wallet <span style="color: #ea4335;">*</span></label>
                        <input type="text" id="senderWallet" required readonly placeholder="Connect wallet..." style="background: #f5f5f5; cursor: not-allowed;">
                        <div class="helper-text">Your connected wallet address</div>
                    </div>

                    <div class="form-group">
                        <label>Your Email (Optional)</label>
                        <input type="email" id="senderEmail" placeholder="your@email.com">
                        <div class="helper-text">Receive a copy of the message link</div>
                    </div>

                    <div class="form-group">
                        <label>To Wallet <span style="color: #ea4335;">*</span></label>
                        <input type="text" id="recipientWallet" required placeholder="Solana wallet address (can be yourself)">
                        <div class="helper-text">Only this wallet can decrypt the message</div>
                    </div>

                    <div class="form-group">
                        <label>Recipient Email (Optional)</label>
                        <input type="email" id="recipientEmail" placeholder="recipient@email.com">
                        <div class="helper-text">Notify the recipient with a message link</div>
                    </div>

                    <div class="form-group">
                        <label>Message <span style="color: #ea4335;">*</span></label>
                        <textarea id="messageContent" required placeholder="Write your encrypted message here..."></textarea>
                        <div class="helper-text">This message will be encrypted with AES-256 before sending</div>
                    </div>

                    <div style="display: flex; gap: 12px; align-items: center;">
                        <button type="submit" class="btn-send" id="sendBtn">
                            Send Encrypted Message
                        </button>
                        <button type="button" class="btn-test" id="testBtn" onclick="sendTestMessage()">
                            Test (Skip Payment)
                        </button>
                    </div>
                </form>
            </div>

            <div id="successPanel" class="success-panel">
                <h2>✓ Message Sent</h2>
                <p>Your encrypted message has been minted as an NFT and delivered to the recipient's wallet.</p>
                <div>
                    <a href="my_letters.php" class="link">Back to Inbox</a>
                    <a href="compose.php" class="link">Send Another</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let connectedWallet = localStorage.getItem('postoffice_wallet') || null;

        // Auto-fill sender wallet on load
        window.addEventListener('load', async () => {
            if (connectedWallet) {
                document.getElementById('senderWallet').value = connectedWallet;
                await checkAndUpdateSubscriptionStatus();
            } else {
                // Try auto-connect
                await connectWalletSilent();
                if (connectedWallet) {
                    await checkAndUpdateSubscriptionStatus();
                }
            }

            // Check for reply data
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('reply') === 'true') {
                const replyData = localStorage.getItem('postoffice_reply_data');
                if (replyData) {
                    try {
                        const data = JSON.parse(replyData);

                        // Pre-fill recipient fields
                        document.getElementById('recipientWallet').value = data.recipientWallet || '';
                        document.getElementById('recipientEmail').value = data.recipientEmail || '';

                        // Pre-fill message with quoted text
                        const messageField = document.getElementById('messageContent');
                        messageField.value = '\n\n------- Original Message -------\n' + data.quotedMessage;

                        // Position cursor at the beginning
                        messageField.focus();
                        messageField.setSelectionRange(0, 0);
                        messageField.scrollTop = 0;

                        // Clear reply data from localStorage
                        localStorage.removeItem('postoffice_reply_data');

                        // Update URL to remove reply parameter
                        window.history.replaceState({}, '', 'compose.php');
                    } catch (e) {
                        console.error('Failed to parse reply data:', e);
                    }
                }
            }
        });

        async function checkAndUpdateSubscriptionStatus() {
            if (!connectedWallet) return;

            try {
                const response = await fetch('/notary/api.php?action=check_subscription', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ wallet: connectedWallet })
                });

                const data = await response.json();
                console.log('Subscription status:', data);

                // FREE FOR ALL PHASE - Test button enabled for everyone!
                const testBtn = document.getElementById('testBtn');
                testBtn.disabled = false;

                if (data.success && data.hasAccess) {
                    // Show premium badge for subscribers
                    const badge = document.createElement('div');
                    badge.style.cssText = 'background: #34a853; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; display: inline-block; margin-bottom: 16px;';
                    badge.textContent = `✓ Premium Subscriber (expires ${new Date(data.subscription_end).toLocaleDateString()})`;
                    document.querySelector('.compose-header').after(badge);
                }
            } catch (error) {
                console.error('Failed to check subscription:', error);
            }
        }

        async function connectWalletSilent() {
            if (typeof window.solana !== 'undefined' && window.solana.isPhantom) {
                try {
                    const response = await window.solana.connect({ onlyIfTrusted: true });
                    if (response && response.publicKey) {
                        connectedWallet = response.publicKey.toString();
                        localStorage.setItem('postoffice_wallet', connectedWallet);
                        document.getElementById('senderWallet').value = connectedWallet;
                        await checkAndUpdateSubscriptionStatus();
                    }
                } catch (err) {
                    console.log('Silent connect not available');
                }
            }
        }

        async function connectWallet() {
            if (typeof window.solana === 'undefined') {
                showStatus('error', 'Please install Phantom wallet');
                return null;
            }

            try {
                const response = await window.solana.connect({ onlyIfTrusted: false });
                const wallet = response.publicKey.toString();
                localStorage.setItem('postoffice_wallet', wallet);
                return wallet;
            } catch (err) {
                showStatus('error', 'Wallet connection cancelled');
                return null;
            }
        }

        function showStatus(type, message) {
            const statusEl = document.getElementById('statusMessage');
            statusEl.className = 'status-message ' + type;
            statusEl.textContent = message;
            statusEl.style.display = 'block';
        }

        async function sendTestMessage() {
            const senderWallet = document.getElementById('senderWallet').value.trim();
            const senderEmail = document.getElementById('senderEmail').value.trim();
            const recipientWallet = document.getElementById('recipientWallet').value.trim();
            const recipientEmail = document.getElementById('recipientEmail').value.trim();
            const messageContent = document.getElementById('messageContent').value.trim();

            if (!senderWallet || !recipientWallet || !messageContent) {
                showStatus('error', 'Please fill all required fields');
                return;
            }

            // Validate wallet addresses
            if (recipientWallet.length < 32 || recipientWallet.length > 44) {
                showStatus('error', 'Invalid recipient wallet address');
                return;
            }

            const testBtn = document.getElementById('testBtn');
            const sendBtn = document.getElementById('sendBtn');
            testBtn.disabled = true;
            sendBtn.disabled = true;

            // Connect wallet if not already connected
            if (!connectedWallet) {
                showStatus('info', 'Connecting to Phantom wallet...');
                connectedWallet = await connectWallet();
                if (!connectedWallet) {
                    testBtn.disabled = false;
                    sendBtn.disabled = false;
                    return;
                }
                document.getElementById('senderWallet').value = connectedWallet;
            }

            showStatus('info', 'Creating test message (bypassing payment)...');

            const slug = generateSlug();

            try {
                const payload = {
                    email: senderEmail,
                    slug: slug,
                    recipient: recipientWallet,
                    recipientEmail: recipientEmail,
                    content: messageContent,
                    creatorWallet: connectedWallet
                };

                console.log('Test payload:', payload);

                const response = await fetch('/notary/api.php?action=mint_sealed_direct', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                console.log('Test response:', data);

                if (data.success) {
                    showStatus('success', '✅ Test message sent!');
                    document.getElementById('formPanel').style.display = 'none';
                    document.getElementById('successPanel').style.display = 'block';
                } else {
                    console.error('Test error:', data.error);
                    showStatus('error', 'Error: ' + (data.error || 'Failed to send test message'));
                    testBtn.disabled = false;
                    sendBtn.disabled = false;
                }
            } catch (error) {
                console.error('Test exception:', error);
                showStatus('error', 'Error: ' + error.message);
                testBtn.disabled = false;
                sendBtn.disabled = false;
            }
        }

        function generateSlug() {
            const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
            let slug = 'po';
            for (let i = 0; i < 12; i++) {
                slug += chars[Math.floor(Math.random() * chars.length)];
            }
            return slug;
        }

        document.getElementById('messageForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const sendBtn = document.getElementById('sendBtn');
            const senderWallet = document.getElementById('senderWallet').value.trim();
            const senderEmail = document.getElementById('senderEmail').value.trim();
            const recipientWallet = document.getElementById('recipientWallet').value.trim();
            const recipientEmail = document.getElementById('recipientEmail').value.trim();
            const messageContent = document.getElementById('messageContent').value.trim();

            if (!senderWallet || !recipientWallet || !messageContent) {
                showStatus('error', 'Please fill all required fields');
                return;
            }

            // Validate wallet addresses
            if (recipientWallet.length < 32 || recipientWallet.length > 44) {
                showStatus('error', 'Invalid recipient wallet address');
                return;
            }

            sendBtn.disabled = true;

            // Connect wallet if not already connected
            if (!connectedWallet) {
                showStatus('info', 'Connecting to Phantom wallet...');
                connectedWallet = await connectWallet();
                if (!connectedWallet) {
                    sendBtn.disabled = false;
                    return;
                }
                document.getElementById('senderWallet').value = connectedWallet;
            }

            showStatus('info', 'Creating encrypted message...');

            const slug = generateSlug();

            try {
                // Call API to create sealed letter
                const payload = {
                    email: senderEmail,
                    slug: slug,
                    recipient: recipientWallet,
                    recipientEmail: recipientEmail,
                    content: messageContent,
                    creatorWallet: connectedWallet
                };

                console.log('Sending payload:', payload);
                console.log('Content length:', messageContent.length);

                const response = await fetch('/notary/api.php?action=create_sealed_letter', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                console.log('Backend response:', data);

                if (data.success) {
                    // Check if admin bypass (instant mint)
                    if (data.admin_bypass) {
                        showStatus('success', '✅ Message encrypted and sent!');
                        document.getElementById('formPanel').style.display = 'none';
                        document.getElementById('successPanel').style.display = 'block';
                    } else {
                        // Redirect to Stripe payment
                        showStatus('info', 'Redirecting to payment...');
                        window.location.href = data.url;
                    }
                } else {
                    console.error('Backend error:', data.error);
                    showStatus('error', 'Error: ' + (data.error || 'Failed to create message'));
                    sendBtn.disabled = false;
                }
            } catch (error) {
                console.error('Exception:', error);
                showStatus('error', 'Error: ' + error.message);
                sendBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
