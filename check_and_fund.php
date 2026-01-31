<?php
/**
 * Check SOL balance and auto-fund if needed
 * Sends 0.005 SOL from treasury wallet if user has insufficient balance
 */

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$wallet = $input['wallet'] ?? '';

if (empty($wallet)) {
    http_response_code(400);
    echo json_encode(['error' => 'Wallet address required']);
    exit;
}

// Solana RPC endpoint (devnet)
$rpc_url = 'https://api.devnet.solana.com';

// Check wallet balance
function getBalance($wallet, $rpc_url) {
    $data = [
        'jsonrpc' => '2.0',
        'id' => 1,
        'method' => 'getBalance',
        'params' => [$wallet]
    ];

    $ch = curl_init($rpc_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    return $result['result']['value'] ?? 0;
}

// Get current balance (in lamports)
$balance = getBalance($wallet, $rpc_url);
$balanceSOL = $balance / 1000000000; // Convert lamports to SOL

// Minimum balance threshold (0.001 SOL)
$minBalance = 0.001;
$fundAmount = 0.005;

if ($balanceSOL < $minBalance) {
    // Need to fund the wallet
    // Call Node.js script to send SOL from treasury

    $command = sprintf(
        'cd %s && /usr/bin/node %s %s %s 2>&1',
        escapeshellarg('/var/www/gudtek.lol/is/postoffice'),
        escapeshellarg('send_sol.js'),
        escapeshellarg($wallet),
        escapeshellarg($fundAmount)
    );

    error_log('Funding wallet: ' . $command);
    $output = shell_exec($command);
    error_log('Funding output: ' . $output);

    // Check if funding was successful
    if (strpos($output, 'Success') !== false || strpos($output, 'success') !== false) {
        // Wait a moment for transaction to confirm
        sleep(2);

        // Check new balance
        $newBalance = getBalance($wallet, $rpc_url);
        $newBalanceSOL = $newBalance / 1000000000;

        echo json_encode([
            'success' => true,
            'funded' => true,
            'previousBalance' => $balanceSOL,
            'newBalance' => $newBalanceSOL,
            'amountSent' => $fundAmount,
            'message' => "Added {$fundAmount} SOL to your wallet for transaction fees"
        ]);
    } else {
        error_log('Funding failed: ' . $output);
        echo json_encode([
            'success' => false,
            'funded' => false,
            'balance' => $balanceSOL,
            'error' => 'Failed to fund wallet. Please request devnet SOL from a faucet.',
            'output' => $output
        ]);
    }
} else {
    // Wallet has sufficient balance
    echo json_encode([
        'success' => true,
        'funded' => false,
        'balance' => $balanceSOL,
        'message' => 'Wallet has sufficient balance'
    ]);
}
?>
