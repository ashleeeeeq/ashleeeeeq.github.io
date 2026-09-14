<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        if (!session()->get('user_id') || session()->get('role') !== 'user') {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        return view('homepage');
    }
}
