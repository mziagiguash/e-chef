<?php

// Two-way encryption method to encrypt/decrypt all data (e.g., from URL)
function encryptor($action, $string)
{
    $output = false;

    $encrypt_method = "AES-256-CBC";
    // Set your unique hashing key
    $secret_key = 'beatnik#technolgoy_sampreeti';
    $secret_iv = 'beatnik$technolgoy@sampreeti';

    // Generate hash key
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16); // 16 bytes required

    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } elseif ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

// Get decrypted current user ID from session
function currentUserId()
{
    return encryptor('decrypt', request()->session()->get('userId'));
}

// Get decrypted access type from session
function fullAccess()
{
    return encryptor('decrypt', request()->session()->get('accessType'));
}

// Get decrypted role identity from session
function currentUser()
{
    return encryptor('decrypt', request()->session()->get('roleIdentity'));
}

// Get current locale
function currentLocale()
{
    return app()->getLocale();
}

// Check if user is logged in
function isLoggedIn()
{
    return session()->has('userId') && !is_null(session('userId'));
}

// Currency symbol from session (default $)
function currencySymbol()
{
    return session('currency_symbol', '$');
}

// Currency rate from session (default 1.0)
function currencyRate()
{
    return session('currency_rate', 1.0);
}

// Helper to build localized route
function localeRoute($name, $parameters = [])
{
    return route($name, array_merge(['locale' => app()->getLocale()], $parameters));
}

