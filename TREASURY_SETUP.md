# Treasury Wallet Setup

The auto-funding feature requires a treasury wallet to send SOL to users who have insufficient balance.

## Setup Steps

### 1. Generate Treasury Keypair

```bash
cd /var/www/gudtek.lol/is/postoffice
solana-keygen new -o .treasury-keypair.json --no-bip39-passphrase
```

**IMPORTANT**: Store the generated public key for reference.

### 2. Fund the Treasury Wallet (Devnet)

Get the public key:
```bash
solana-keygen pubkey .treasury-keypair.json
```

Fund it from the Solana devnet faucet:
```bash
solana airdrop 2 <TREASURY_PUBLIC_KEY> --url devnet
```

Or use the web faucet: https://faucet.solana.com

Recommended minimum: **5 SOL** on devnet to support multiple users.

### 3. Test the Setup

```bash
# Test sending SOL
node send_sol.js <TEST_WALLET_ADDRESS> 0.005

# Expected output: "Success! Transaction signature: ..."
```

### 4. Monitor Treasury Balance

```bash
solana balance <TREASURY_PUBLIC_KEY> --url devnet
```

When balance gets low, refill from the faucet.

## Security Notes

- **Devnet Only**: This setup is for devnet. Never use for mainnet.
- **File Permissions**: The `.treasury-keypair.json` should have restricted permissions:
  ```bash
  chmod 600 .treasury-keypair.json
  ```
- **Backup**: Keep a backup of the keypair in a secure location
- **Monitoring**: Set up alerts when treasury balance drops below 1 SOL

## How It Works

1. Before send/burn operations, `check_and_fund.php` checks user's SOL balance
2. If balance < 0.001 SOL, it calls `send_sol.js` to send 0.005 SOL from treasury
3. User can then complete their transaction with the funded balance

## Troubleshooting

**Error: "Treasury keypair not found"**
- Run step 1 to generate the keypair

**Error: "Insufficient treasury balance"**
- Fund the treasury wallet using step 2

**Error: "Failed to fund wallet"**
- Check treasury balance
- Check network connectivity
- Review logs in `/var/log/apache2/error.log`
