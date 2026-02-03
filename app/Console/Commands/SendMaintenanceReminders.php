<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Website;
use App\Notifications\UpcomingMaintenanceReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendMaintenanceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie des notifications 24h avant les maintenances prévues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow();

        // Récupérer tous les sites avec une maintenance prévue demain
        $websites = Website::with(['client'])
            ->whereDate('next_maintenance_date', $tomorrow)
            ->get();

        if ($websites->isEmpty()) {
            $this->info('Aucune maintenance prévue pour demain.');
            return 0;
        }

        $this->info("🔔 {$websites->count()} maintenance(s) prévue(s) pour demain ({$tomorrow->format('d.m.Y')}):");

        foreach ($websites as $website) {
            $this->line("  - {$website->name} ({$website->client->name})");
        }

        // Envoyer les notifications à tous les Managers et Techniciens
        $users = User::whereIn('role', [User::ROLE_MANAGER, User::ROLE_TECHNICIAN])->get();

        foreach ($users as $user) {
            $user->notify(new UpcomingMaintenanceReminder($websites));
            $this->info("✉️  Notification envoyée à {$user->name} ({$user->email})");
        }

        $this->info('✅ Toutes les notifications ont été envoyées avec succès!');

        return 0;
    }
}
