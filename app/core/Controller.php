<?php

namespace App\Core;

class Controller
{
    protected $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    protected function view($view, $data = [])
    {
        \App\Core\View::make($view, $data);
    }
    
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url)
    {
        // If URL is relative, make it absolute
        if (strpos($url, 'http') !== 0 && strpos($url, '/') === 0) {
            $app = \App\Core\Application::getInstance();
            $appUrl = $app->getConfig('url');
            $url = rtrim($appUrl, '/') . $url;
        }
        
        header('Location: ' . $url);
        exit;
    }
    
    protected function back()
    {
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
    
    protected function input($key, $default = null)
    {
        return $_REQUEST[$key] ?? $default;
    }
    
    protected function only($keys)
    {
        return array_intersect_key($_REQUEST, array_flip($keys));
    }
    
    protected function all()
    {
        return $_REQUEST;
    }
    
    protected function has($key)
    {
        return isset($_REQUEST[$key]);
    }
    
    protected function validate($rules, $data = null)
    {
        if ($data === null) {
            $data = $this->all();
        }
        
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field][] = ucfirst($field) . ' is required';
                }
                
                if ($r === 'email' && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = ucfirst($field) . ' must be a valid email';
                }
                
                if (strpos($r, 'min:') === 0 && !empty($data[$field])) {
                    $min = substr($r, 4);
                    if (strlen($data[$field]) < $min) {
                        $errors[$field][] = ucfirst($field) . ' must be at least ' . $min . ' characters';
                    }
                }
                
                if (strpos($r, 'max:') === 0 && !empty($data[$field])) {
                    $max = substr($r, 4);
                    if (strlen($data[$field]) > $max) {
                        $errors[$field][] = ucfirst($field) . ' must not exceed ' . $max . ' characters';
                    }
                }
                
                if (strpos($r, 'confirmed') !== false && !empty($data[$field])) {
                    $confirmField = $field . '_confirmation';
                    if (!isset($data[$confirmField]) || $data[$field] !== $data[$confirmField]) {
                        $errors[$field][] = ucfirst($field) . ' confirmation does not match';
                    }
                }
            }
        }
        
        return $errors;
    }
}
