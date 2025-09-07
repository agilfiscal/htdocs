<?php

namespace App\Controllers;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        require APP_ROOT . '/app/Views/' . $view . '.php';
    }
} 