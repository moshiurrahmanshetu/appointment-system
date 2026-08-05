<?php

namespace App\Core;

class Request
{
    private $method;
    private $uri;
    private $data;
    private $files;
    
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->data = array_merge($_GET, $_POST);
        $this->files = $_FILES;
    }
    
    public function method()
    {
        return $this->method;
    }
    
    public function uri()
    {
        return $this->uri;
    }
    
    public function input($key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }
    
    public function all()
    {
        return $this->data;
    }
    
    public function only($keys)
    {
        return array_intersect_key($this->data, array_flip($keys));
    }
    
    public function except($keys)
    {
        return array_diff_key($this->data, array_flip($keys));
    }
    
    public function has($key)
    {
        return isset($this->data[$key]);
    }
    
    public function file($key)
    {
        return $this->files[$key] ?? null;
    }
    
    public function hasFile($key)
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }
    
    public function isPost()
    {
        return $this->method === 'POST';
    }
    
    public function isGet()
    {
        return $this->method === 'GET';
    }
    
    public function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    public function wantsJson()
    {
        return $this->isAjax() || 
               (isset($_SERVER['HTTP_ACCEPT']) && 
                strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
