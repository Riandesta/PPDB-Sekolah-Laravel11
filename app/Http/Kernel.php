<?php

namespace App\Http\Kernel;
use UserMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

Class Kernel extends HttpKernel
{
    protected $routeMiddleware = [
        'user' => \App\Http\Middleware\UserMiddleware::class,
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'panitia' => \App\Http\Middleware\AdminMiddleware::class,
    ];

}
