# Postoffice

Decentralized encrypted messaging system based on Solana blockchain. Messages are minted as compressed NFTs with AES-256-CBC encryption, stored on IPFS, and only decryptable by the recipient wallet holder.

## Architecture

- **Blockchain**: Solana (Devnet)
- **NFT Standard**: Compressed NFTs (Metaplex Bubblegum)
- **Encryption**: AES-256-CBC, client-side
- **Storage**: IPFS via Pinata
- **Access Control**: Wallet-based ownership verification
- **API**: Helius DAS for cNFT queries
- **Wallet**: Phantom integration
- **ZKP Ready**: Architecture prepared for zero-knowledge proof integration (coming soon)

## File Structure

```
postoffice/
├── index.php                    - Landing page
├── compose.php                  - Message composition interface
├── my_letters.php              - Inbox view with cNFT list
├── view.php                    - Individual message viewer
├── pricing.php                 - Subscription plans (SOL/Token/Stripe)
├── api.php                     - API proxy to backend
├── subscription_payment.php    - Payment handler
└── init_subscriptions_db.php   - Database initialization
```

## Database Schema

### sealed_letters
Shared with notary system - stores encrypted message metadata and IPFS CID.

### postoffice_subscriptions
```sql
- wallet_address: TEXT (unique)
- subscription_type: TEXT
- payment_method: TEXT (sol|token|stripe)
- amount: REAL
- transaction_id: TEXT
- start_date: TEXT
- end_date: TEXT
- status: TEXT (active|expired)
```

### postoffice_payments
Transaction log for all subscription payments.

## Message Flow

1. Sender composes message in compose.php
2. Client-side AES-256-CBC encryption using recipient's public key
3. Encrypted payload uploaded to IPFS
4. cNFT minted to recipient's wallet with IPFS CID in metadata
5. Recipient views inbox (my_letters.php) via Helius DAS API
6. Ownership verified via RPC before decryption
7. Client-side decryption if ownership confirmed

## Subscription Model

- **Base**: 0.1 SOL/month
- **Token Discount**: 50% off with $POST tokens
- **Stripe**: +20% processing fee markup

Payment methods implemented in `subscription_payment.php`:
- SOL transfer verification
- Token transfer (placeholder)
- Stripe Checkout integration

## Deployment

Live at: https://postoffice.gudtek.lol

Backend shared with `/notary/` system - same database, same encryption stack, different frontend.

## Security Notes

- Encryption happens client-side before transmission
- Private keys never leave the user's wallet
- Server only stores encrypted blobs and IPFS CIDs
- Ownership verification required for decryption attempts
- No central authority can decrypt messages

## Development Status

Experimental protocol on Solana Devnet. Not audited. Use at own risk.

## Roadmap

- **Zero-Knowledge Proofs**: Implementation planned for privacy-preserving message verification without revealing content
