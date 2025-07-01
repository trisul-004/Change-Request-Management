<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class AssignRoles extends Command
{
    protected $signature = 'roles:assign {email} {role}';
    protected $description = 'Assign a role to a user';

    public function handle()
    {
        $email = $this->argument('email');
        $roleName = $this->argument('role');

        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $role = Role::firstOrCreate(['name' => $roleName]);
        $user->assignRole($role);

        $this->info("Role {$roleName} assigned to user {$email} successfully.");
        return 0;
    }
} 