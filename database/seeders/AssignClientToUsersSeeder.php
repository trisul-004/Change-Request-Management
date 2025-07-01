<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AssignClientToUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users that don't have client_id assigned
        $users = User::whereNull('client_id')->get();
        
        $clientIds = ['c1', 'c2', 'c3', 'c4', 'c5'];
        $clientIndex = 0;
        
        foreach ($users as $user) {
            // Skip admin users - they don't need client_id
            if ($user->hasRole('admin')) {
                continue;
            }
            
            // Assign client_id in round-robin fashion
            $user->update(['client_id' => $clientIds[$clientIndex % count($clientIds)]]);
            $clientIndex++;
        }
        
        $this->command->info('Client IDs assigned to users successfully!');
    }
} 