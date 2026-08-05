<?php

use App\Core\Application;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\View;

if (!function_exists('config')) {
    function config($key = null)
    {
        $app = Application::getInstance();
        return $app->getConfig($key);
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        // If URL is relative, make it absolute
        if (strpos($url, 'http') !== 0 && strpos($url, '/') === 0) {
            $appUrl = config('url');
            $url = rtrim($appUrl, '/') . $url;
        }
        
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('back')) {
    function back()
    {
        redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}

if (!function_exists('view')) {
    function view($view, $data = [])
    {
        View::make($view, $data);
    }
}

if (!function_exists('old')) {
    function old($key, $default = '')
    {
        return Session::getFlash('_old_' . $key, $default);
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return Csrf::field();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        return Csrf::getToken();
    }
}

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('auth')) {
    function auth()
    {
        return Session::get('user');
    }
}

if (!function_exists('guest')) {
    function guest()
    {
        return !auth();
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        $appUrl = config('url');
        return rtrim($appUrl, '/') . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url($path = '')
    {
        $appUrl = config('url');
        return rtrim($appUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('route')) {
    function route($name, $params = [])
    {
        // For now, return the path as-is
        // In a full implementation, this would resolve named routes
        return '/' . ltrim($name, '/');
    }
}

if (!function_exists('session')) {
    function session($key = null, $default = null)
    {
        if ($key === null) {
            return Session::class;
        }
        
        return Session::get($key, $default);
    }
}

if (!function_exists('flash')) {
    function flash($key, $value = null)
    {
        if ($value === null) {
            return Session::getFlash($key);
        }
        
        Session::setFlash($key, $value);
    }
}

if (!function_exists('has_flash')) {
    function has_flash($key)
    {
        return Session::hasFlash($key);
    }
}

if (!function_exists('error')) {
    function error($key, $default = '')
    {
        $errors = Session::getFlash('errors', []);
        return $errors[$key] ?? $default;
    }
}

if (!function_exists('has_error')) {
    function has_error($key)
    {
        $errors = Session::getFlash('errors', []);
        return isset($errors[$key]);
    }
}

if (!function_exists('set_old')) {
    function set_old($data)
    {
        foreach ($data as $key => $value) {
            Session::set('_old_' . $key, $value);
        }
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die(1);
    }
}

if (!function_exists('abort')) {
    function abort($code = 404, $message = '')
    {
        http_response_code($code);
        
        $errorView = __DIR__ . '/../views/errors/' . $code . '.php';
        
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo '<h1>Error ' . $code . '</h1>';
            if ($message) {
                echo '<p>' . e($message) . '</p>';
            }
        }
        
        exit;
    }
}
