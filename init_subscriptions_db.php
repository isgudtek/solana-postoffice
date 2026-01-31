<?php
/**
 * Initialize Subscriptions Database
 * Run this once to create the subscriptions tables
 */

$db = new SQLite3(__DIR__ . '/../data.db');

// Create subscriptions table
$db->exec('CREATE TABLE IF NOT EXISTS postoffice_subscriptions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    wallet_address TEXT NOT NULL UNIQUE,
    subscription_type TEXT NOT NULL,
    payment_method TEXT NOT NULL,
    amount REAL NOT NULL,
    transaction_id TEXT,
    start_date TEXT NOT NULL,
    end_date TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT "active",
    created_at TEXT NOT NULL,
    updated_at TEXT NOT NULL
)');

// Create subscription payments table (for tracking all payments)
$db->exec('CREATE TABLE IF NOT EXISTS postoffice_payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    wallet_address TEXT NOT NULL,
    payment_method TEXT NOT NULL,
    amount REAL NOT NULL,
    transaction_id TEXT,
    status TEXT NOT NULL DEFAULT "pending",
    created_at TEXT NOT NULL,
    metadata TEXT
)');

// Create indexes
$db->exec('CREATE INDEX IF NOT EXISTS idx_subscriptions_wallet ON postoffice_subscriptions(wallet_address)');
$db->exec('CREATE INDEX IF NOT EXISTS idx_subscriptions_status ON postoffice_subscriptions(status)');
$db->exec('CREATE INDEX IF NOT EXISTS idx_payments_wallet ON postoffice_payments(wallet_address)');
$db->exec('CREATE INDEX IF NOT EXISTS idx_payments_status ON postoffice_payments(status)');

echo "✓ Subscriptions database initialized successfully!\n";
echo "Tables created:\n";
echo "  - postoffice_subscriptions\n";
echo "  - postoffice_payments\n";

$db->close();
?>
