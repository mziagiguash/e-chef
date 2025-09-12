<?php
if (!function_exists('extractYouTubeId')) {
    function extractYouTubeId($url)
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        preg_match($pattern, $url, $matches);
        return $matches[1] ?? null;
    }
}

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
