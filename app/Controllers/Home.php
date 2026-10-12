<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home/index', [
            'title'    => 'Vitrina · Catálogos para cotizar por WhatsApp',
            'loggedIn' => (bool) session()->get('user_id'),
        ]);
    }
}
