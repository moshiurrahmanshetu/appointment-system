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

if (!function_exists('user')) {
    function user()
    {
        return Session::get('user');
    }
}

if (!function_exists('hasRole')) {
    function hasRole($role)
    {
        $user = auth();
        if (!$user) {
            return false;
        }
        
        // Load role dynamically from database
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT r.slug FROM roles r 
                INNER JOIN users u ON u.role_id = r.id 
                WHERE u.id = :user_id";
        $result = $db->fetch($sql, ['user_id' => $user['id']]);
        
        if (!$result) {
            return false;
        }
        
        // Check if user has the role (support both string and array)
        if (is_array($role)) {
            return in_array($result['slug'], $role);
        }
        
        return $result['slug'] === $role;
    }
}

if (!function_exists('can')) {
    function can($permission)
    {
        $user = auth();
        if (!$user) {
            return false;
        }
        
        // Load user permissions dynamically from database
        $db = \App\Core\Database::getInstance();
        $sql = "SELECT p.slug FROM permissions p 
                INNER JOIN role_permissions rp ON rp.permission_id = p.id
                INNER JOIN users u ON u.role_id = rp.role_id
                WHERE u.id = :user_id";
        $permissions = $db->fetchAll($sql, ['user_id' => $user['id']]);
        
        $permissionSlugs = array_column($permissions, 'slug');
        
        // Check if user has the permission (support both string and array)
        if (is_array($permission)) {
            return !empty(array_intersect($permission, $permissionSlugs));
        }
        
        return in_array($permission, $permissionSlugs);
    }
}

if (!function_exists('cannot')) {
    function cannot($permission)
    {
        return !can($permission);
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
        $assetUrl = config('asset_url');
        return rtrim($assetUrl, '/') . '/public/assets/' . ltrim($path, '/');
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
