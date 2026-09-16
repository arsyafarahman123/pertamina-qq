<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

date_default_timezone_set('Asia/Jakarta');

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi keamanan telah diperbarui. Silakan ulangi login.',
                    'csrf_token' => csrf_token(),
                ], 419);
            }
            return redirect()->route('login')->withErrors([
                'email' => 'Sesi login telah disegarkan. Silakan masukkan kembali kredensial Anda.',
            ]);
        });
    })->create();

return $app;
