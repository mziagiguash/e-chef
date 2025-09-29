<?php

namespace App\Http\Helpers;

class IdHelper
{
    public static function encode($id)
    {
        return base64_encode($id);
    }

    public static function decode($encodedId)
    {
        $decoded = base64_decode($encodedId);
        return is_numeric($decoded) ? $decoded : $encodedId;
    }

    public static function isEncoded($id)
    {
        $decoded = base64_decode($id, true);
        return $decoded !== false && is_numeric($decoded);
    }
}
