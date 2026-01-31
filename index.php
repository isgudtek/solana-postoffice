<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postoffice — Encrypted Mail Service</title>
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
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            border-bottom: 1px solid #e5e5e5;
            background: white;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 32px;
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
            padding: 120px 32px;
        }

        .hero-content {
            max-width: 720px;
            margin: 0 auto;
            text-align: center;
        }

        .hero h1 {
            font-size: 56px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 20px;
            letter-spacing: -0.04em;
            line-height: 1.1;
        }

        .hero-subtitle {
            font-size: 18px;
            color: #525252;
            margin-bottom: 48px;
            line-height: 1.6;
            max-width: 580px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-btn {
            background: #0a0a0a;
            color: white;
            border: 1px solid #0a0a0a;
            border-radius: 0;
            padding: 14px 32px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.15s ease;
            font-family: inherit;
            letter-spacing: -0.01em;
        }

        .cta-btn:hover {
            background: #262626;
            border-color: #262626;
        }

        .cta-btn-secondary {
            background: white;
            color: #0a0a0a;
            border: 1px solid #d4d4d4;
        }

        .cta-btn-secondary:hover {
            background: #fafafa;
            border-color: #0a0a0a;
        }

        .wallet-viewer {
            margin-top: 64px;
            padding-top: 64px;
            border-top: 1px solid #e5e5e5;
        }

        .wallet-viewer-title {
            font-size: 14px;
            font-weight: 600;
            color: #0a0a0a;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .wallet-input-group {
            display: flex;
            gap: 12px;
            max-width: 600px;
            margin: 0 auto;
        }

        .wallet-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #d4d4d4;
            border-radius: 0;
            font-family: 'SF Mono', 'Monaco', monospace;
            font-size: 13px;
            background: #fafafa;
            transition: all 0.15s ease;
        }

        .wallet-input:focus {
            outline: none;
            border-color: #0a0a0a;
            background: white;
        }

        .wallet-input::placeholder {
            color: #a3a3a3;
        }

        .stats {
            background: white;
            border-bottom: 1px solid #e5e5e5;
            padding: 64px 32px;
        }

        .stats-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 64px;
            text-align: center;
        }

        .stat-number {
            font-size: 48px;
            color: #0a0a0a;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.03em;
            font-variant-numeric: tabular-nums;
        }

        .stat-label {
            color: #737373;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
        }

        .features {
            padding: 80px 32px;
            background: #fafafa;
        }

        .features-header {
            text-align: center;
            margin-bottom: 64px;
        }

        .features-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: #0a0a0a;
            letter-spacing: -0.03em;
            margin-bottom: 16px;
        }

        .features-header p {
            font-size: 16px;
            color: #525252;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 1px;
            background: #e5e5e5;
            border: 1px solid #e5e5e5;
        }

        .feature-card {
            background: white;
            padding: 40px 32px;
            transition: background 0.15s ease;
        }

        .feature-card:hover {
            background: #fafafa;
        }

        .feature-icon {
            font-size: 32px;
            margin-bottom: 20px;
            display: block;
        }

        .feature-title {
            font-size: 16px;
            font-weight: 600;
            color: #0a0a0a;
            margin-bottom: 12px;
            letter-spacing: -0.01em;
        }

        .feature-desc {
            color: #525252;
            font-size: 14px;
            line-height: 1.6;
        }

        .tech-section {
            background: white;
            border-top: 1px solid #e5e5e5;
            border-bottom: 1px solid #e5e5e5;
            padding: 80px 32px;
        }

        .tech-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .tech-content h2 {
            font-size: 24px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 40px;
            text-align: center;
            letter-spacing: -0.02em;
        }

        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1px;
            background: #e5e5e5;
            border: 1px solid #e5e5e5;
        }

        .tech-item {
            padding: 20px 24px;
            background: #fafafa;
            color: #0a0a0a;
            font-size: 13px;
            font-weight: 500;
            border-left: 2px solid transparent;
            transition: all 0.15s ease;
        }

        .tech-item:hover {
            background: white;
            border-left-color: #0a0a0a;
        }

        .cta-section {
            background: #0a0a0a;
            padding: 80px 32px;
            text-align: center;
        }

        .cta-section h2 {
            font-size: 40px;
            font-weight: 700;
            color: white;
            margin-bottom: 24px;
            letter-spacing: -0.03em;
        }

        .cta-section p {
            font-size: 16px;
            color: #a3a3a3;
            margin-bottom: 40px;
        }

        .cta-section .cta-btn {
            background: white;
            color: #0a0a0a;
            border-color: white;
            font-size: 16px;
            padding: 16px 40px;
        }

        .cta-section .cta-btn:hover {
            background: #fafafa;
            border-color: #fafafa;
        }

        footer {
            background: #fafafa;
            border-top: 1px solid #e5e5e5;
            padding: 48px 32px;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-logo {
            font-size: 14px;
            font-weight: 700;
            color: #0a0a0a;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: -0.02em;
        }

        .footer-text {
            color: #737373;
            font-size: 12px;
            margin-bottom: 16px;
        }

        .footer-links {
            display: flex;
            gap: 24px;
            justify-content: center;
            margin-top: 24px;
        }

        .footer-link {
            color: #525252;
            text-decoration: none;
            font-size: 12px;
            transition: color 0.15s ease;
        }

        .footer-link:hover {
            color: #0a0a0a;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 40px;
            }
            .hero-subtitle {
                font-size: 16px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .tech-grid {
                grid-template-columns: 1fr;
            }
            .cta-section h2 {
                font-size: 32px;
            }
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

    <section class="hero">
        <div class="hero-content">
            <h1>Encrypted Mail<br>on Solana</h1>
            <p class="hero-subtitle">
                Send secure, encrypted messages as NFTs. Only the recipient can decrypt.
                Permanent, immutable, and truly private communication on the blockchain.
            </p>
            <div class="cta-group">
                <a href="compose.php" class="cta-btn">Compose Message</a>
                <a href="my_letters.php" class="cta-btn cta-btn-secondary">View Inbox</a>
            </div>

            <div class="wallet-viewer">
                <div class="wallet-viewer-title">View Any Wallet's Inbox</div>
                <form class="wallet-input-group" onsubmit="event.preventDefault(); viewWalletInbox();">
                    <input
                        type="text"
                        id="walletAddressInput"
                        class="wallet-input"
                        placeholder="Enter Solana wallet address..."
                        pattern="[1-9A-HJ-NP-Za-km-z]{32,44}"
                        required
                    >
                    <button type="submit" class="cta-btn">Check Inbox</button>
                </form>
                <p style="font-size: 12px; color: #737373; margin-top: 12px;">
                    Tech demo: View any inbox, but decryption requires NFT ownership
                </p>
            </div>
        </div>
    </section>

    <script>
        function viewWalletInbox() {
            const walletAddress = document.getElementById('walletAddressInput').value.trim();
            if (walletAddress) {
                window.location.href = 'my_letters.php?wallet=' + encodeURIComponent(walletAddress);
            }
        }
    </script>

    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">∞</div>
                <div class="stat-label">Permanent Storage</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">AES-256</div>
                <div class="stat-label">Encryption</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">&lt;1¢</div>
                <div class="stat-label">Per Message</div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="features-header">
            <h2>Built for Privacy</h2>
            <p>Military-grade encryption meets blockchain permanence. Your messages, your control.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <span class="feature-icon">🔐</span>
                <div class="feature-title">End-to-End Encryption</div>
                <div class="feature-desc">
                    Messages encrypted with AES-256-CBC. Only the NFT holder can decrypt the content.
                </div>
            </div>

            <div class="feature-card">
                <span class="feature-icon">⛓️</span>
                <div class="feature-title">Blockchain Native</div>
                <div class="feature-desc">
                    Built on Solana using compressed NFTs. Permanent, immutable, and verifiable.
                </div>
            </div>

            <div class="feature-card">
                <span class="feature-icon">💎</span>
                <div class="feature-title">True Ownership</div>
                <div class="feature-desc">
                    Your message is an NFT you control. Transfer, burn, or keep it forever.
                </div>
            </div>

            <div class="feature-card">
                <span class="feature-icon">🚀</span>
                <div class="feature-title">Fast Delivery</div>
                <div class="feature-desc">
                    Powered by Solana's high-speed network. Messages delivered in seconds.
                </div>
            </div>

            <div class="feature-card">
                <span class="feature-icon">💸</span>
                <div class="feature-title">Low Cost</div>
                <div class="feature-desc">
                    Compressed NFTs keep costs minimal. Send unlimited messages affordably.
                </div>
            </div>

            <div class="feature-card">
                <span class="feature-icon">🌐</span>
                <div class="feature-title">Distributed Storage</div>
                <div class="feature-desc">
                    Content stored on IPFS. Censorship-resistant and globally accessible.
                </div>
            </div>
        </div>
    </section>

    <section class="tech-section" id="tech">
        <div class="tech-content">
            <h2>Technical Stack</h2>
            <div class="tech-grid">
                <div class="tech-item">Solana Blockchain</div>
                <div class="tech-item">Compressed NFTs (Metaplex)</div>
                <div class="tech-item">AES-256-CBC Encryption</div>
                <div class="tech-item">IPFS Content Storage</div>
                <div class="tech-item">Wallet-Based Access Control</div>
                <div class="tech-item">On-Chain Verification</div>
                <div class="tech-item">Helius DAS API</div>
                <div class="tech-item">Phantom Wallet Integration</div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <h2>Start Sending Encrypted Mail</h2>
        <p>Join the next generation of private communication</p>
        <a href="compose.php" class="cta-btn">Send Your First Message →</a>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-logo">Postoffice</div>
            <div class="footer-text">Encrypted Mail Service on Solana</div>
            <div class="footer-links">
                <a href="/" class="footer-link">gudtek.lol</a>
                <span style="color: #d4d4d4;">|</span>
                <a href="https://github.com/isgudtek/solana-postoffice" target="_blank" class="footer-link">GitHub</a>
                <span style="color: #d4d4d4;">|</span>
                <span class="footer-link" style="cursor: default;">Experimental Protocol</span>
                <span style="color: #d4d4d4;">|</span>
                <span class="footer-link" style="cursor: default;">Devnet Only</span>
            </div>
        </div>
    </footer>
</body>
</html>
