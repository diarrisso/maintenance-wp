<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    protected $signature = 'user:create
                            {--name= : Nom de l\'utilisateur}
                            {--email= : Adresse e-mail}
                            {--password= : Mot de passe}
                            {--role=developer : Rôle (developer ou manager)}';

    protected $description = 'Créer un nouvel utilisateur pour l\'application';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nom de l\'utilisateur');
        $email = $this->option('email') ?: $this->ask('Adresse e-mail');
        $password = $this->option('password') ?: $this->secret('Mot de passe');
        $role = $this->option('role');

        if (!in_array($role, ['developer', 'manager'])) {
            $role = $this->choice('Rôle', ['developer', 'manager'], 0);
        }

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:developer,manager',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);

        $this->info("Utilisateur créé avec succès!");
        $this->table(
            ['ID', 'Nom', 'E-mail', 'Rôle'],
            [[$user->id, $user->name, $user->email, $user->role_label]]
        );

        return self::SUCCESS;
    }
}
