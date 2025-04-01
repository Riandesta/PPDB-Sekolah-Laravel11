<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AdminController extends Controller
{
    //
    public function index()
    {
        $dataLayout = [
            'title' => 'Admin Dashboard',
            'card_title' => 'This is admin dashboard page',
        ]; 

        View::share($dataLayout);


        return view('admin.index', [
            'username' => 'Anto Santana'
        ]);
    }
}
