<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Person;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────
        // STEP 1: Find YOUR person in the tree
        // ─────────────────────────────────────────────

        // This assumes you are the root of the tree (parent_id = null)
        // If you're NOT the root, change this query
        //$yourPerson = Person::whereNull('parent_id')->first();

        // Alternative: If you know your person ID
          $yourPerson = Person::find(2);  // Replace 1 with your actual ID

        // Alternative: If you know your name
        // $yourPerson = Person::where('name', 'Your Name')->first();

        // Check if person exists
        if (!$yourPerson) {
            $this->command->error('❌ No person found! Create your person in the tree first.');
            $this->command->info('Run: php artisan tinker');
            $this->command->info('Then: Person::create([\'name\' => \'Your Name\'])');
            return;
        }

        // ─────────────────────────────────────────────
        // STEP 2: Check if admin already exists
        // ─────────────────────────────────────────────

        $existingAdmin = User::where('email', 'gribzbadi@gmail.com')->first();

        if ($existingAdmin) {
            $this->command->warn('⚠️  Admin user already exists!');
            $this->command->info('Email: ' . $existingAdmin->email);
            $this->command->info('Role: ' . $existingAdmin->role);
            return;
        }

        // ─────────────────────────────────────────────
        // STEP 3: Create the admin user account
        // ─────────────────────────────────────────────

        $admin = User::create([
            'name' => $yourPerson->name,  // Uses your name from the tree

            // ⚠️ CHANGE THIS to your real email
            'email' => 'gribzbadi@gmail.com',

            // ⚠️ CHANGE THIS to a strong password
            'password' => Hash::make('AdminPassword123'),

            'person_id' => $yourPerson->id,  // Links to your tree node
            'role' => 'admin',               // Makes you an admin
            'status' => 'active',            // Account is active (not pending)
            'email_verified_at' => now(),    // Auto-verify your email
        ]);

        // ─────────────────────────────────────────────
        // STEP 4: Show success message
        // ─────────────────────────────────────────────

        $this->command->info('');
        $this->command->info('✅ Admin user created successfully!');
        $this->command->info('');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Name', $admin->name],
                ['Email', $admin->email],
                ['Password', 'AdminPassword123 (CHANGE THIS!)'],
                ['Role', $admin->role],
                ['Linked to person', $yourPerson->name . ' (ID: ' . $yourPerson->id . ')'],
            ]
        );
        $this->command->info('');
        $this->command->warn('⚠️  IMPORTANT: Change this password immediately after logging in!');
        $this->command->info('');
    }
}
