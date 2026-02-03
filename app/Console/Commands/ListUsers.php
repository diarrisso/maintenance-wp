<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    protected $signature = 'user:list';

    protected $description = 'Lister tous les utilisateurs';

    public function handle(): int
    {
        $users = User::orderBy('name')->get();

        if ($users->isEmpty()) {
            $this->warn('Aucun utilisateur trouvé.');
            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Nom', 'E-mail', 'Rôle', 'Créé le'],
            $users->map(fn($u) => [
                $u->id,
                $u->name,
                $u->email,
                $u->role_label,
                $u->created_at->format('d.m.Y H:i'),
            ])
        );

        return self::SUCCESS;
    }
}
