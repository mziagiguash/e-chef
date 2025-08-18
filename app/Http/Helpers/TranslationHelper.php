<?php
namespace App\Helpers;

class TranslationHelper
{
    public static function fixTranslationArray($arr)
    {
        if (!is_array($arr)) {
            return ['en' => '', 'ru' => '', 'ka' => ''];
        }
        foreach (['en', 'ru', 'ka'] as $lang) {
            if (!isset($arr[$lang]) || $arr[$lang] === null) {
                $arr[$lang] = '';
            }
        }
        return $arr;
    }
}
