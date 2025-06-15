<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Configuration;
use App\Console\Commands\UpdateExpiredItems;
use App\Console\Commands\UpdateTransaksiStatus;
use App\Console\Commands\CekTransaksiHangus;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'pegawai.role' => \App\Http\Middleware\PegawaiRoleMiddleware::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Job lama: setiap 1 menit
        $schedule->command(UpdateExpiredItems::class)->everyMinute();

        // Job baru: setiap 1 menit
        $schedule->command('transaksi:update-status')->everyMinute();

        // Cek Hangus
        $schedule->command(CekTransaksiHangus::class)->everyMinute();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
