<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CheckRoles extends Command
{
    protected $signature = 'roles:check';
    protected $description = 'Check roles in the database';

    public function handle()
    {
        $this->info('Checking roles...');
        
        // Check roles
        $roles = Role::all();
        $this->info('Available roles:');
        foreach ($roles as $role) {
            $this->line("- {$role->name}");
        }

        // Check user roles
        $users = User::all();
        $this->info('\nUser roles:');
        foreach ($users as $user) {
            $this->line("{$user->name} ({$user->email}): " . implode(', ', $user->getRoleNames()->toArray()));
        }
    }
} 