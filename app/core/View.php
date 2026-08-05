<?php

namespace App\Core;

class View
{
    private static $data = [];
    private static $layout = 'main';
    
    public static function make($view, $data = [])
    {
        self::$data = array_merge(self::$data, $data);
        
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            die('View not found: ' . $view);
        }
        
        extract(self::$data);
        
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        if (self::$layout) {
            $layoutPath = __DIR__ . '/../views/layouts/' . self::$layout . '.php';
            
            if (file_exists($layoutPath)) {
                ob_start();
                require $layoutPath;
                ob_end_flush();
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }
    
    public static function layout($layout)
    {
        self::$layout = $layout;
    }
    
    public static function share($key, $value)
    {
        self::$data[$key] = $value;
    }
    
    public static function escape($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
