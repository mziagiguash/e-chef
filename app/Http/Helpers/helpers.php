<?php

if (!function_exists('decryptor')) {
    function decryptor($type, $string)
    {
        if ($type == 'encrypt') {
            return base64_encode($string); // или encrypt($string);
        } elseif ($type == 'decrypt') {
            return base64_decode($string); // или decrypt($string);
        }
    }

}
