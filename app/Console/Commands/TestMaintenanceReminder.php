<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Website;
use App\Notifications\UpcomingMaintenanceReminder;
use Illuminate\Console\Command;

class TestMaintenanceReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:test-reminder {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie une notification de test à l\'email spécifié';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("❌ Utilisateur avec l'email {$email} introuvable.");
            return 1;
        }

        // Récupérer quelques websites pour l'exemple (max 3)
        $websites = Website::with(['client'])->take(3)->get();

        if ($websites->isEmpty()) {
            $this->error('❌ Aucun website trouvé dans la base de données.');
            return 1;
        }

        $this->info("📧 Envoi d'une notification de test à {$user->name} ({$email})...");
        $this->info("📋 Websites inclus dans le test:");

        foreach ($websites as $website) {
            $this->line("  - {$website->name} ({$website->client->name})");
        }

        $user->notify(new UpcomingMaintenanceReminder($websites));

        $this->info('✅ Notification de test envoyée avec succès!');
        $this->info('💡 Vérifiez votre boîte de réception (ou Mailtrap si configuré)');

        return 0;
    }
}
