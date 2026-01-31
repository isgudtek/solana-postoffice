# POSTOFFICE - Encrypted Messaging on Solana

## 🚀 Overview
Professional Gmail-style messaging interface for encrypted messages on Solana blockchain. Clean, accessible design focused on usability and trust, while maintaining all blockchain/encryption functionality.

## 📁 File Structure
```
/var/www/gudtek.lol/is/postoffice/
├── index.php          - Landing page with cyber-tech hype
├── compose.php        - Send encrypted messages
├── my_letters.php     - View owned encrypted messages
├── view.php           - View individual encrypted messages
├── api.php            - API proxy to notary backend
└── README.md          - This file
```

## 🎨 Design Theme
- **Gmail-inspired**: Clean, professional, accessible
- **Color palette**: Gmail blues (#4285f4), clean whites, subtle grays
- **Typography**: Google Sans, Roboto, sans-serif
- **Layout**: List/table view for inbox
- **Rounded corners** (8-16px border-radius)
- **Subtle shadows** for depth
- **Light theme** with professional aesthetics
- **Generous padding** and whitespace

## 🎯 Branding
- **Name**: POSTOFFICE
- **Tagline**: "Encrypted Messaging on Solana"
- **Positioning**: Professional secure communication, blockchain-native
- **Target**: Businesses, professionals, privacy-conscious users
- **Messaging**: Secure, reliable, permanent, professional

## 🔗 URLs
- Landing: https://is.gudtek.lol/postoffice/
- Compose: https://is.gudtek.lol/postoffice/compose.php
- My Messages: https://is.gudtek.lol/postoffice/my_letters.php

## ⚙️ Backend
- Shares same backend as `/notary/` via API proxy
- Same database tables (`sealed_letters`)
- Same encryption (AES-256-CBC)
- Same Solana cNFT infrastructure
- Same IPFS storage (Pinata)
- **Completely isolated frontend** - different branding, styling, messaging

## 🔐 Technical Specs (Hyped)
- Solana Blockchain (Devnet)
- Compressed NFTs (Metaplex Bubblegum)
- AES-256-CBC Encryption
- IPFS Content Storage
- Wallet-Based Access Control
- On-Chain Ownership Verification
- Helius DAS API Integration
- Phantom Wallet Compatible

## 💎 Features
1. **End-to-End Encryption** - AES-256-CBC encryption
2. **Blockchain Native** - Permanent, immutable on Solana
3. **True Ownership** - Your message is an NFT you control
4. **Fast Delivery** - Powered by Solana
5. **Low Cost** - Minimal fees with compressed NFTs
6. **IPFS Storage** - Distributed, censorship-resistant

## 🎮 User Flow
1. Visit landing page (hyped intro)
2. Click "Send Encrypted Message"
3. Fill in: email, recipient wallet, message
4. Connect Phantom wallet
5. Message encrypted client-side
6. Minted as cNFT to recipient wallet
7. Only recipient can decrypt

## 📊 Stats (Displayed on Landing)
- ∞ Permanent Storage
- 256-bit AES Encryption  
- <1¢ Cost Per Message

## 🚨 Notes
- Currently on Devnet only
- Experimental protocol
- Backend shared with notary system (invisible to users)
- Frontend completely independent
- Can be marketed separately without mentioning notary
