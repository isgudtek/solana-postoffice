<?php
/**
 * Post Office Pricing Page
 * Monthly subscription plans with SOL, Stripe, and Token payments
 */

// Get database connection
$db = new SQLite3(__DIR__ . '/../data.db');

// Get current SOL price
function fetchSolPrice() {
    $cache_file = __DIR__ . '/../sol_price_cache.json';
    $cache_duration = 60; // 1 minute cache

    // Check if cache exists and is fresh
    if (file_exists($cache_file)) {
        $cache_data = json_decode(file_get_contents($cache_file), true);
        if ($cache_data && (time() - $cache_data['timestamp']) < $cache_duration) {
            return $cache_data['price'];
        }
    }

    // Fallback to default price if API fails
    return 140.0;
}

$sol_price_usd = fetchSolPrice();
$subscription_sol = 0.1; // 0.1 SOL per month
$subscription_usd = round($subscription_sol * $sol_price_usd * 1.20, 2); // +20% for card processing
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - Post Office</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'SF Pro Display', 'Segoe UI', sans-serif;
            background: #ffffff;
            color: #0a0a0a;
            line-height: 1.6;
        }

        .header {
            background: white;
            border-bottom: 1px solid #e5e5e5;
            padding: 16px 32px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
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

        .hero {
            background: white;
            border-bottom: 1px solid #e5e5e5;
            padding: 80px 32px 60px;
            text-align: center;
        }

        .hero h1 {
            font-size: 42px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 16px;
            letter-spacing: -0.04em;
        }

        .hero-subtitle {
            font-size: 16px;
            color: #737373;
            max-width: 600px;
            margin: 0 auto 40px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 60px 32px;
        }

        .token-info-box {
            margin-bottom: 48px;
            padding: 24px;
            border: 1px solid #e5e5e5;
            background: #fafafa;
            text-align: center;
        }

        .token-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .token-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        .token-title {
            font-size: 18px;
            font-weight: 600;
            color: #0a0a0a;
        }

        .token-mint {
            font-family: 'SF Mono', 'Monaco', monospace;
            font-size: 12px;
            color: #737373;
            word-break: break-all;
            padding: 8px 12px;
            background: white;
            border: 1px solid #e5e5e5;
            margin-top: 12px;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 48px;
        }

        .price-card {
            border: 1px solid #e5e5e5;
            background: white;
            padding: 32px 24px;
            text-align: center;
            position: relative;
            transition: all 0.2s ease;
        }

        .price-card:hover {
            border-color: #0a0a0a;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .price-badge {
            position: absolute;
            top: -12px;
            right: 16px;
            background: #0a0a0a;
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .price-badge.discount {
            background: #16a34a;
        }

        .price-badge.markup {
            background: #dc2626;
        }

        .price-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #737373;
            margin-bottom: 16px;
            font-weight: 600;
        }

        .price-amount {
            font-size: 36px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .price-period {
            font-size: 13px;
            color: #737373;
            margin-bottom: 24px;
        }

        .price-btn {
            width: 100%;
            padding: 14px 24px;
            background: #0a0a0a;
            color: white;
            border: 1px solid #0a0a0a;
            border-radius: 0;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.15s ease;
            font-family: inherit;
        }

        .price-btn:hover {
            background: transparent;
            color: #0a0a0a;
        }

        .price-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .wallet-status {
            padding: 16px;
            background: #fef3c7;
            border: 1px solid #fbbf24;
            text-align: center;
            margin-bottom: 32px;
            font-size: 14px;
        }

        .wallet-info {
            padding: 16px;
            background: #dcfce7;
            border: 1px solid #16a34a;
            text-align: center;
            margin-bottom: 32px;
            font-size: 14px;
            display: none;
        }

        .wallet-address {
            font-family: 'SF Mono', 'Monaco', monospace;
            font-size: 12px;
            color: #0a0a0a;
            margin-top: 8px;
        }

        .features-list {
            margin-top: 48px;
            padding-top: 48px;
            border-top: 1px solid #e5e5e5;
        }

        .features-list h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 24px;
            text-align: center;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 16px;
            background: #fafafa;
        }

        .feature-icon {
            font-size: 20px;
            line-height: 1;
        }

        .feature-text {
            font-size: 14px;
            color: #525252;
        }

        @media (max-width: 768px) {
            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 32px;
            }
        }

        .status-message {
            padding: 12px 16px;
            margin-top: 16px;
            text-align: center;
            font-size: 14px;
            border-radius: 4px;
            display: none;
        }

        .status-success {
            background: #dcfce7;
            border: 1px solid #16a34a;
            color: #166534;
        }

        .status-error {
            background: #fee2e2;
            border: 1px solid #dc2626;
            color: #991b1b;
        }

        .status-info {
            background: #dbeafe;
            border: 1px solid #3b82f6;
            color: #1e40af;
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
                <a href="pricing.php" class="header-link" style="color: #0a0a0a;">Pricing</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <h1>Simple, Transparent Pricing</h1>
        <p class="hero-subtitle">
            Choose your preferred payment method. All plans include unlimited encrypted messages.
        </p>
    </section>

    <div class="container">
        <!-- Token Info Box -->
        <div class="token-info-box">
            <div class="token-header">
                <div class="token-icon">📬</div>
                <div class="token-title">Ecosystem Utility Token <strong>$POST</strong></div>
            </div>
            <p style="font-size: 13px; color: #737373; margin-bottom: 8px;">
                Pay with $POST tokens for 50% discount on all subscriptions
            </p>
            <div class="token-mint" id="tokenMintDisplay">
                Loading token contract...
            </div>
        </div>

        <!-- Wallet Status -->
        <div class="wallet-status" id="walletStatus">
            <strong>Connect your wallet to subscribe</strong>
            <div style="margin-top: 12px;">
                <button id="connectBtn" class="price-btn" style="max-width: 300px; margin: 0 auto; display: block;">
                    Connect Phantom Wallet
                </button>
            </div>
        </div>

        <div class="wallet-info" id="walletInfo">
            <strong>✓ Wallet Connected</strong>
            <div class="wallet-address" id="walletAddress"></div>
        </div>

        <!-- Pricing Cards -->
        <div class="pricing-grid">
            <!-- SOL Payment -->
            <div class="price-card">
                <div class="price-label">Pay with SOL</div>
                <div class="price-amount"><?php echo number_format($subscription_sol, 2); ?></div>
                <div class="price-period">SOL per month</div>
                <button class="price-btn" id="paySolBtn" disabled>Subscribe with SOL</button>
                <div class="status-message" id="statusSol"></div>
            </div>

            <!-- Token Payment (50% OFF) -->
            <div class="price-card">
                <div class="price-badge discount">50% OFF</div>
                <div class="price-label">Pay with $POST Tokens</div>
                <div class="price-amount"><?php echo number_format($subscription_sol * 0.5, 3); ?></div>
                <div class="price-period">SOL equivalent per month</div>
                <button class="price-btn" id="payTokenBtn" disabled>Subscribe with $POST</button>
                <div class="status-message" id="statusToken"></div>
            </div>

            <!-- Stripe Payment (+20%) -->
            <div class="price-card">
                <div class="price-badge markup">+20% Processing Fee</div>
                <div class="price-label">Pay with Card</div>
                <div class="price-amount">$<?php echo number_format($subscription_usd, 2); ?></div>
                <div class="price-period">USD per month</div>
                <button class="price-btn" id="payStripeBtn" disabled>Subscribe with Card 💳</button>
                <div class="status-message" id="statusStripe"></div>
            </div>
        </div>

        <!-- Features List -->
        <div class="features-list">
            <h3>What's Included</h3>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">✉️</div>
                    <div class="feature-text"><strong>Unlimited Messages</strong><br>Send and receive encrypted messages</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🔒</div>
                    <div class="feature-text"><strong>End-to-End Encryption</strong><br>Only recipients can decrypt</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🎨</div>
                    <div class="feature-text"><strong>NFT Messages</strong><br>Every letter is a unique NFT</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">⚡</div>
                    <div class="feature-text"><strong>Solana Network</strong><br>Fast and low-cost transactions</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">📦</div>
                    <div class="feature-text"><strong>Permanent Storage</strong><br>Messages stored on blockchain</div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🌐</div>
                    <div class="feature-text"><strong>Decentralized</strong><br>No central authority</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let connectedWallet = null;

        // Placeholder token mint (replace with actual token)
        const TOKEN_MINT = 'POST1234567890abcdefghijklmnopqrstuvwxyz'; // Placeholder

        // Display token mint
        document.getElementById('tokenMintDisplay').textContent = TOKEN_MINT;

        // Connect wallet
        document.getElementById('connectBtn').addEventListener('click', async () => {
            try {
                if (typeof window.solana === 'undefined' || !window.solana.isPhantom) {
                    alert('Please install Phantom wallet to continue.');
                    return;
                }

                const response = await window.solana.connect();
                const publicKey = response.publicKey.toString();
                connectedWallet = publicKey;

                // Update UI
                document.getElementById('walletStatus').style.display = 'none';
                document.getElementById('walletInfo').style.display = 'block';
                document.getElementById('walletAddress').textContent = publicKey;

                // Enable payment buttons
                document.getElementById('paySolBtn').disabled = false;
                document.getElementById('payTokenBtn').disabled = false;
                document.getElementById('payStripeBtn').disabled = false;

            } catch (err) {
                console.error('Wallet connection failed:', err);
                alert('Failed to connect wallet. Please try again.');
            }
        });

        // SOL Payment
        document.getElementById('paySolBtn').addEventListener('click', async () => {
            const statusEl = document.getElementById('statusSol');
            const btn = document.getElementById('paySolBtn');

            statusEl.textContent = 'Preparing SOL transaction...';
            statusEl.className = 'status-message status-info';
            statusEl.style.display = 'block';
            btn.disabled = true;

            try {
                // Create SOL transfer transaction
                const solAmount = <?php echo $subscription_sol; ?>; // 0.1 SOL
                const recipient = 'GudTekPostOfficePaymentWallet111111111111'; // TODO: Replace with actual payment wallet

                // Request transaction
                const transaction = await window.solana.request({
                    method: 'signAndSendTransaction',
                    params: {
                        message: `Post Office Subscription: ${solAmount} SOL`
                    }
                });

                statusEl.textContent = 'Processing payment...';

                // Record the subscription
                const response = await fetch('subscription_payment.php?action=create_sol_subscription', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        wallet_address: connectedWallet,
                        transaction_id: transaction.signature || 'pending',
                        amount: solAmount
                    })
                });

                const data = await response.json();

                if (data.success) {
                    statusEl.textContent = '✓ Subscription activated! Valid until ' + new Date(data.end_date).toLocaleDateString();
                    statusEl.className = 'status-message status-success';
                } else {
                    throw new Error(data.error || 'Payment failed');
                }

            } catch (err) {
                console.error('SOL payment error:', err);
                statusEl.textContent = '✗ Payment failed: ' + (err.message || 'Please try again');
                statusEl.className = 'status-message status-error';
                btn.disabled = false;
            }
        });

        // Token Payment
        document.getElementById('payTokenBtn').addEventListener('click', async () => {
            const statusEl = document.getElementById('statusToken');
            const btn = document.getElementById('payTokenBtn');

            statusEl.textContent = 'Preparing $POST token transaction...';
            statusEl.className = 'status-message status-info';
            statusEl.style.display = 'block';
            btn.disabled = true;

            try {
                const tokenAmount = <?php echo $subscription_sol * 0.5; ?>; // 50% discount

                statusEl.textContent = 'Requesting token transfer...';

                // TODO: Implement actual token transfer
                // For now, simulating the payment
                await new Promise(resolve => setTimeout(resolve, 1500));

                // Record the subscription
                const response = await fetch('subscription_payment.php?action=create_token_subscription', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        wallet_address: connectedWallet,
                        transaction_id: 'token_' + Date.now(),
                        amount: tokenAmount
                    })
                });

                const data = await response.json();

                if (data.success) {
                    statusEl.textContent = '✓ ' + data.message + ' Valid until ' + new Date(data.end_date).toLocaleDateString();
                    statusEl.className = 'status-message status-success';
                } else {
                    throw new Error(data.error || 'Payment failed');
                }

            } catch (err) {
                console.error('Token payment error:', err);
                statusEl.textContent = '✗ Payment failed: ' + (err.message || 'Please try again');
                statusEl.className = 'status-message status-error';
                btn.disabled = false;
            }
        });

        // Stripe Payment
        document.getElementById('payStripeBtn').addEventListener('click', async () => {
            const statusEl = document.getElementById('statusStripe');
            const btn = document.getElementById('payStripeBtn');

            statusEl.textContent = 'Creating Stripe checkout session...';
            statusEl.className = 'status-message status-info';
            statusEl.style.display = 'block';
            btn.disabled = true;

            try {
                const response = await fetch('subscription_payment.php?action=create_stripe_subscription', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        wallet_address: connectedWallet
                    })
                });

                const data = await response.json();

                if (data.success && data.url) {
                    statusEl.textContent = 'Redirecting to Stripe...';
                    // Redirect to Stripe checkout
                    window.location.href = data.url;
                } else {
                    throw new Error(data.error || 'Failed to create checkout session');
                }

            } catch (err) {
                console.error('Stripe payment error:', err);
                statusEl.textContent = '✗ Failed to start checkout: ' + (err.message || 'Please try again');
                statusEl.className = 'status-message status-error';
                btn.disabled = false;
            }
        });

        // Check for successful Stripe payment
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('payment') === 'success') {
            const sessionId = urlParams.get('session_id');
            if (sessionId) {
                // Show success message
                const successBanner = document.createElement('div');
                successBanner.className = 'wallet-info';
                successBanner.style.display = 'block';
                successBanner.innerHTML = '<strong>✓ Payment successful!</strong><br>Your subscription has been activated.';
                document.querySelector('.container').insertBefore(successBanner, document.querySelector('.token-info-box'));
            }
        } else if (urlParams.get('payment') === 'cancelled') {
            const cancelBanner = document.createElement('div');
            cancelBanner.className = 'wallet-status';
            cancelBanner.innerHTML = '<strong>Payment cancelled</strong><br>You can try again using any payment method below.';
            document.querySelector('.container').insertBefore(cancelBanner, document.querySelector('.token-info-box'));
        }
    </script>
</body>
</html>
