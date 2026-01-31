#!/usr/bin/env node
/**
 * Send SOL from treasury wallet to recipient
 * Usage: node send_sol.js <recipient_address> <amount_in_sol>
 */

const { Connection, Keypair, LAMPORTS_PER_SOL, SystemProgram, Transaction, sendAndConfirmTransaction } = require('@solana/web3.js');
const fs = require('fs');
const path = require('path');

// Devnet connection
const connection = new Connection('https://api.devnet.solana.com', 'confirmed');

// Load treasury wallet keypair
// TODO: Store this securely in environment variable or config
const TREASURY_KEYPAIR_PATH = '/var/www/gudtek.lol/is/postoffice/.treasury-keypair.json';

async function sendSOL(recipientAddress, amountSOL) {
    try {
        // Check if treasury keypair exists
        if (!fs.existsSync(TREASURY_KEYPAIR_PATH)) {
            console.error('ERROR: Treasury keypair not found at ' + TREASURY_KEYPAIR_PATH);
            console.error('Please create a treasury wallet first using: solana-keygen new -o ' + TREASURY_KEYPAIR_PATH);
            process.exit(1);
        }

        // Load treasury keypair
        const keypairData = JSON.parse(fs.readFileSync(TREASURY_KEYPAIR_PATH, 'utf-8'));
        const treasuryKeypair = Keypair.fromSecretKey(new Uint8Array(keypairData));

        console.log('Treasury wallet:', treasuryKeypair.publicKey.toString());
        console.log('Recipient:', recipientAddress);
        console.log('Amount:', amountSOL, 'SOL');

        // Check treasury balance
        const treasuryBalance = await connection.getBalance(treasuryKeypair.publicKey);
        const treasuryBalanceSOL = treasuryBalance / LAMPORTS_PER_SOL;
        console.log('Treasury balance:', treasuryBalanceSOL, 'SOL');

        if (treasuryBalanceSOL < amountSOL) {
            console.error('ERROR: Insufficient treasury balance');
            console.error('Treasury has ' + treasuryBalanceSOL + ' SOL but needs ' + amountSOL + ' SOL');
            process.exit(1);
        }

        // Create transfer transaction
        const transaction = new Transaction().add(
            SystemProgram.transfer({
                fromPubkey: treasuryKeypair.publicKey,
                toPubkey: recipientAddress,
                lamports: amountSOL * LAMPORTS_PER_SOL,
            })
        );

        // Send and confirm transaction
        console.log('Sending transaction...');
        const signature = await sendAndConfirmTransaction(
            connection,
            transaction,
            [treasuryKeypair],
            {
                commitment: 'confirmed',
            }
        );

        console.log('Success! Transaction signature:', signature);
        console.log('Sent ' + amountSOL + ' SOL to ' + recipientAddress);

        process.exit(0);
    } catch (error) {
        console.error('ERROR:', error.message);
        process.exit(1);
    }
}

// Parse command line arguments
const args = process.argv.slice(2);

if (args.length < 2) {
    console.error('Usage: node send_sol.js <recipient_address> <amount_in_sol>');
    process.exit(1);
}

const recipientAddress = args[0];
const amountSOL = parseFloat(args[1]);

if (isNaN(amountSOL) || amountSOL <= 0) {
    console.error('ERROR: Invalid amount. Must be a positive number.');
    process.exit(1);
}

// Execute
sendSOL(recipientAddress, amountSOL);
