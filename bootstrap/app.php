<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Envoyer les rappels de maintenance tous les jours à 9h00
        $schedule->command('maintenance:send-reminders')
            ->dailyAt('09:00')
            ->timezone('Europe/Berlin');

        // Archiver automatiquement les rapports envoyés depuis plus d'une semaine
        $schedule->command('reports:archive-old')
            ->dailyAt('02:00')
            ->timezone('Europe/Berlin')
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Mail::raw(
                    "Le cron job reports:archive-old s'est exécuté avec succès à " . now()->format('d.m.Y H:i') . ".",
                    fn ($msg) => $msg->to('diarrisso@achtzigdreissig.de')->subject('CRON OK: reports:archive-old')
                );
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('CRON reports:archive-old failed');
                \Illuminate\Support\Facades\Mail::raw(
                    "Le cron job reports:archive-old a échoué à " . now()->format('d.m.Y H:i') . ".\nVérifiez les logs sur le serveur.",
                    fn ($msg) => $msg->to('diarrisso@achtzigdreissig.de')->subject('CRON Fehler: reports:archive-old')
                );
            });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
