<?php
/**
 * POSTOFFICE API - Proxy to Notary API
 * This keeps the postoffice completely isolated while reusing backend logic
 */

// Simply include and execute the notary API
require_once __DIR__ . '/../notary/api.php';
