<?php
/**
 * Post Office Subscription Payment Handler
 * Handles SOL, Token, and Stripe payments for monthly subscriptions
 */

require_once __DIR__ . '/../stripe_config.php';

header('Content-Type: application/json');

// Get database connection
$db = new SQLite3(__DIR__ . '/../data.db');

// Get action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'create_sol_subscription':
        createSolSubscription();
        break;

    case 'create_token_subscription':
        createTokenSubscription();
        break;

    case 'create_stripe_subscription':
        createStripeSubscription();
        break;

    case 'check_subscription':
        checkSubscription();
        break;

    case 'stripe_webhook':
        handleStripeWebhook();
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}

/**
 * Create SOL subscription
 */
function createSolSubscription() {
    global $db;

    $input = json_decode(file_get_contents('php://input'), true);
    $wallet_address = $input['wallet_address'] ?? '';
    $transaction_id = $input['transaction_id'] ?? '';
    $amount = 0.1; // 0.1 SOL per month

    if (empty($wallet_address) || empty($transaction_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    // TODO: Verify transaction on Solana blockchain
    // For now, we'll assume it's valid

    $start_date = date('Y-m-d H:i:s');
    $end_date = date('Y-m-d H:i:s', strtotime('+1 month'));

    // Record payment
    $stmt = $db->prepare('INSERT INTO postoffice_payments (wallet_address, payment_method, amount, transaction_id, status, created_at) VALUES (:wallet, :method, :amount, :tx_id, :status, :created_at)');
    $stmt->bindValue(':wallet', $wallet_address, SQLITE3_TEXT);
    $stmt->bindValue(':method', 'sol', SQLITE3_TEXT);
    $stmt->bindValue(':amount', $amount, SQLITE3_FLOAT);
    $stmt->bindValue(':tx_id', $transaction_id, SQLITE3_TEXT);
    $stmt->bindValue(':status', 'completed', SQLITE3_TEXT);
    $stmt->bindValue(':created_at', date('Y-m-d H:i:s'), SQLITE3_TEXT);
    $stmt->execute();

    // Create or update subscription
    $stmt = $db->prepare('INSERT OR REPLACE INTO postoffice_subscriptions (wallet_address, subscription_type, payment_method, amount, transaction_id, start_date, end_date, status, created_at, updated_at) VALUES (:wallet, :type, :method, :amount, :tx_id, :start, :end, :status, :created, :updated)');
    $stmt->bindValue(':wallet', $wallet_address, SQLITE3_TEXT);
    $stmt->bindValue(':type', 'monthly', SQLITE3_TEXT);
    $stmt->bindValue(':method', 'sol', SQLITE3_TEXT);
    $stmt->bindValue(':amount', $amount, SQLITE3_FLOAT);
    $stmt->bindValue(':tx_id', $transaction_id, SQLITE3_TEXT);
    $stmt->bindValue(':start', $start_date, SQLITE3_TEXT);
    $stmt->bindValue(':end', $end_date, SQLITE3_TEXT);
    $stmt->bindValue(':status', 'active', SQLITE3_TEXT);
    $stmt->bindValue(':created', $start_date, SQLITE3_TEXT);
    $stmt->bindValue(':updated', $start_date, SQLITE3_TEXT);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Subscription activated',
        'end_date' => $end_date
    ]);
}

/**
 * Create Token subscription (50% discount)
 */
function createTokenSubscription() {
    global $db;

    $input = json_decode(file_get_contents('php://input'), true);
    $wallet_address = $input['wallet_address'] ?? '';
    $transaction_id = $input['transaction_id'] ?? '';
    $amount = 0.05; // 0.05 SOL equivalent (50% off)

    if (empty($wallet_address) || empty($transaction_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    // TODO: Verify token transaction on Solana blockchain
    // For now, we'll assume it's valid

    $start_date = date('Y-m-d H:i:s');
    $end_date = date('Y-m-d H:i:s', strtotime('+1 month'));

    // Record payment
    $stmt = $db->prepare('INSERT INTO postoffice_payments (wallet_address, payment_method, amount, transaction_id, status, created_at) VALUES (:wallet, :method, :amount, :tx_id, :status, :created_at)');
    $stmt->bindValue(':wallet', $wallet_address, SQLITE3_TEXT);
    $stmt->bindValue(':method', 'token', SQLITE3_TEXT);
    $stmt->bindValue(':amount', $amount, SQLITE3_FLOAT);
    $stmt->bindValue(':tx_id', $transaction_id, SQLITE3_TEXT);
    $stmt->bindValue(':status', 'completed', SQLITE3_TEXT);
    $stmt->bindValue(':created_at', date('Y-m-d H:i:s'), SQLITE3_TEXT);
    $stmt->execute();

    // Create or update subscription
    $stmt = $db->prepare('INSERT OR REPLACE INTO postoffice_subscriptions (wallet_address, subscription_type, payment_method, amount, transaction_id, start_date, end_date, status, created_at, updated_at) VALUES (:wallet, :type, :method, :amount, :tx_id, :start, :end, :status, :created, :updated)');
    $stmt->bindValue(':wallet', $wallet_address, SQLITE3_TEXT);
    $stmt->bindValue(':type', 'monthly', SQLITE3_TEXT);
    $stmt->bindValue(':method', 'token', SQLITE3_TEXT);
    $stmt->bindValue(':amount', $amount, SQLITE3_FLOAT);
    $stmt->bindValue(':tx_id', $transaction_id, SQLITE3_TEXT);
    $stmt->bindValue(':start', $start_date, SQLITE3_TEXT);
    $stmt->bindValue(':end', $end_date, SQLITE3_TEXT);
    $stmt->bindValue(':status', 'active', SQLITE3_TEXT);
    $stmt->bindValue(':created', $start_date, SQLITE3_TEXT);
    $stmt->bindValue(':updated', $start_date, SQLITE3_TEXT);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Subscription activated with 50% token discount!',
        'end_date' => $end_date
    ]);
}

/**
 * Create Stripe checkout session for subscription
 */
function createStripeSubscription() {
    global $db;

    $input = json_decode(file_get_contents('php://input'), true);
    $wallet_address = $input['wallet_address'] ?? '';

    if (empty($wallet_address)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing wallet address']);
        return;
    }

    // Calculate price: 0.1 SOL * SOL price * 1.20 markup
    $sol_price_usd = fetchSolPrice();
    $price_usd_dollars = 0.1 * $sol_price_usd * 1.20;
    $price_usd = round($price_usd_dollars * 100); // Convert to cents

    // Minimum charge is $0.50 (50 cents) for Stripe
    if ($price_usd < 50) {
        $price_usd = 50;
    }

    try {
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Post Office Monthly Subscription',
                        'description' => 'Unlimited encrypted messages for one month'
                    ],
                    'unit_amount' => $price_usd,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => 'https://is.gudtek.lol/postoffice/pricing.php?payment=success&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'https://is.gudtek.lol/postoffice/pricing.php?payment=cancelled',
            'metadata' => [
                'wallet_address' => $wallet_address,
                'product' => 'postoffice_subscription',
                'payment_method' => 'stripe'
            ]
        ]);

        echo json_encode([
            'success' => true,
            'sessionId' => $checkout_session->id,
            'url' => $checkout_session->url
        ]);

    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * Check subscription status
 */
function checkSubscription() {
    global $db;

    $wallet_address = $_GET['wallet_address'] ?? '';

    if (empty($wallet_address)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing wallet address']);
        return;
    }

    $stmt = $db->prepare('SELECT * FROM postoffice_subscriptions WHERE wallet_address = :wallet AND status = :status ORDER BY end_date DESC LIMIT 1');
    $stmt->bindValue(':wallet', $wallet_address, SQLITE3_TEXT);
    $stmt->bindValue(':status', 'active', SQLITE3_TEXT);
    $result = $stmt->execute();

    $subscription = $result->fetchArray(SQLITE3_ASSOC);

    if ($subscription) {
        // Check if subscription is still valid
        $now = time();
        $end_time = strtotime($subscription['end_date']);

        if ($end_time > $now) {
            echo json_encode([
                'active' => true,
                'subscription' => $subscription
            ]);
        } else {
            // Mark as expired
            $stmt = $db->prepare('UPDATE postoffice_subscriptions SET status = :status WHERE id = :id');
            $stmt->bindValue(':status', 'expired', SQLITE3_TEXT);
            $stmt->bindValue(':id', $subscription['id'], SQLITE3_INTEGER);
            $stmt->execute();

            echo json_encode([
                'active' => false,
                'message' => 'Subscription expired'
            ]);
        }
    } else {
        echo json_encode([
            'active' => false,
            'message' => 'No active subscription'
        ]);
    }
}

/**
 * Handle Stripe Webhook
 */
function handleStripeWebhook() {
    global $db;

    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

    try {
        $event = json_decode($payload, true);

        if ($event['type'] === 'checkout.session.completed') {
            $session = $event['data']['object'];

            $wallet_address = $session['metadata']['wallet_address'] ?? '';
            $payment_id = $session['id'];
            $amount = $session['amount_total'] / 100; // Convert from cents to dollars

            if ($wallet_address) {
                $start_date = date('Y-m-d H:i:s');
                $end_date = date('Y-m-d H:i:s', strtotime('+1 month'));

                // Record payment
                $stmt = $db->prepare('INSERT INTO postoffice_payments (wallet_address, payment_method, amount, transaction_id, status, created_at) VALUES (:wallet, :method, :amount, :tx_id, :status, :created_at)');
                $stmt->bindValue(':wallet', $wallet_address, SQLITE3_TEXT);
                $stmt->bindValue(':method', 'stripe', SQLITE3_TEXT);
                $stmt->bindValue(':amount', $amount, SQLITE3_FLOAT);
                $stmt->bindValue(':tx_id', $payment_id, SQLITE3_TEXT);
                $stmt->bindValue(':status', 'completed', SQLITE3_TEXT);
                $stmt->bindValue(':created_at', date('Y-m-d H:i:s'), SQLITE3_TEXT);
                $stmt->execute();

                // Create subscription
                $stmt = $db->prepare('INSERT OR REPLACE INTO postoffice_subscriptions (wallet_address, subscription_type, payment_method, amount, transaction_id, start_date, end_date, status, created_at, updated_at) VALUES (:wallet, :type, :method, :amount, :tx_id, :start, :end, :status, :created, :updated)');
                $stmt->bindValue(':wallet', $wallet_address, SQLITE3_TEXT);
                $stmt->bindValue(':type', 'monthly', SQLITE3_TEXT);
                $stmt->bindValue(':method', 'stripe', SQLITE3_TEXT);
                $stmt->bindValue(':amount', $amount, SQLITE3_FLOAT);
                $stmt->bindValue(':tx_id', $payment_id, SQLITE3_TEXT);
                $stmt->bindValue(':start', $start_date, SQLITE3_TEXT);
                $stmt->bindValue(':end', $end_date, SQLITE3_TEXT);
                $stmt->bindValue(':status', 'active', SQLITE3_TEXT);
                $stmt->bindValue(':created', $start_date, SQLITE3_TEXT);
                $stmt->bindValue(':updated', $start_date, SQLITE3_TEXT);
                $stmt->execute();
            }
        }

        http_response_code(200);
        echo json_encode(['received' => true]);

    } catch (\Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

/**
 * Fetch live SOL price in USD from CoinGecko API
 */
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

    // Fetch fresh price from CoinGecko
    try {
        $url = 'https://api.coingecko.com/api/v3/simple/price?ids=solana&vs_currencies=usd';
        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
                'user_agent' => 'Mozilla/5.0'
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['solana']['usd'])) {
                $price = floatval($data['solana']['usd']);

                // Cache the price
                file_put_contents($cache_file, json_encode([
                    'price' => $price,
                    'timestamp' => time()
                ]));

                return $price;
            }
        }
    } catch (\Exception $e) {
        error_log('Failed to fetch SOL price: ' . $e->getMessage());
    }

    // Fallback to default price if API fails
    return 140.0;
}
?>
