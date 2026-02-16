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
        // Exécution à 02:00, retry à 03:00 et 04:00 en cas d'échec
        $archiveJob = function () use ($schedule, &$archiveNotify) {
            return $schedule->command('reports:archive-old')
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
                        "Le cron job reports:archive-old a échoué à " . now()->format('d.m.Y H:i') . ". Un retry automatique est prévu.\nVérifiez les logs sur le serveur.",
                        fn ($msg) => $msg->to('diarrisso@achtzigdreissig.de')->subject('CRON Fehler: reports:archive-old')
                    );
                });
        };

        $archiveJob()->dailyAt('02:00');
        $archiveJob()->dailyAt('03:00')->when(function () {
            // Retry à 03:00 seulement s'il reste des rapports à archiver
            return \App\Models\MaintenanceReport::where('status', 'sent')
                ->where('sent_at', '<=', now()->subWeek())
                ->exists();
        });
        $archiveJob()->dailyAt('04:00')->when(function () {
            // Dernier retry à 04:00
            return \App\Models\MaintenanceReport::where('status', 'sent')
                ->where('sent_at', '<=', now()->subWeek())
                ->exists();
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
