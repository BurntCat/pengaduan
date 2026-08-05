<?php

namespace App\Controllers;

class Pengaduan extends BaseController
{
    public function index()
    {
        return view('pengaduan/form');
    }
}