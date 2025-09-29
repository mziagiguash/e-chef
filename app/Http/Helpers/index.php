<?php
// app/Http/Helpers/index.php

// Two way encryption method to encrypt all data from url
function encryptor($action, $string)
{
    $output = false;

    $encrypt_method = "AES-256-CBC";
    // pls set your unique hashing key
    $secret_key = 'beatnik#technolgoy_sampreeti';
    $secret_iv = 'beatnik$technolgoy@sampreeti';

    //hash
    $key = hash('sha256', $secret_key);

    //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    //do the encryption given text/string/number
    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if ($action == 'decrypt') {
        //decrypt the given text/string/number
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function currentUserId()
{
    return encryptor('decrypt', request()->session()->get('userId'));
}

function fullAccess()
{
    return encryptor('decrypt', request()->session()->get('accessType'));
}

function currentUser()
{
    return encryptor('decrypt', request()->session()->get('roleIdentity'));
}

function currentLocale()
{
    return app()->getLocale();
}

function isLoggedIn()
{
    return session()->has('userId') && !is_null(session('userId'));
}

function currencySymbol()
{
    return session('currency_symbol', '$');
}

function currencyRate()
{
    return session('currency_rate', 1.0);
}

// Helper to build localized route
function localeRoute($name, $parameters = [], $absolute = true)
{
    if (!is_array($parameters)) {
        $parameters = [$parameters];
    }

    // Получаем информацию о маршруте из системы маршрутизации Laravel
    $route = app('router')->getRoutes()->getByName($name);

    if ($route) {
        // Получаем список всех параметров, которые ожидает маршрут
        $parametersNames = $route->parameterNames();

        // Если маршрут требует параметр 'locale', добавляем его автоматически
        if (in_array('locale', $parametersNames)) {
            return route($name, array_merge(['locale' => app()->getLocale()], $parameters), $absolute);
        }
    }

    // Для маршрутов, которые не требуют locale
    return route($name, $parameters, $absolute);
}

// Альтернативная функция для маршрутов с параметрами
function localizedRoute($name, $parameters = [], $absolute = true)
{
    $locale = app()->getLocale();

    // Если параметры не массив, преобразуем в массив
    if (!is_array($parameters)) {
        $parameters = ['id' => $parameters];
    }

    // Добавляем locale в параметры
    $parameters = array_merge(['locale' => $locale], $parameters);

    return route($name, $parameters, $absolute);
}

// Функция для получения правильного URL курса
function courseUrl($courseId)
{
    return localizedRoute('courses.show', $courseId);
}

// Функция для получения правильного URL инструктора
function instructorUrl($instructorId)
{
    return localizedRoute('instructor.show', $instructorId);
}

// Функция для получения правильного URL категории
function categoryUrl($categoryId)
{
    return route('searchCourse', ['locale' => app()->getLocale(), 'category' => $categoryId]);
}
