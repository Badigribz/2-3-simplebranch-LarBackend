<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('person_id')
                ->nullable()
                ->after('email')
                ->constrained('people')
                ->nullOnDelete();  // If person deleted, account stays but unlinked

            $table->enum('role', ['viewer', 'editor', 'admin'])
                ->default('viewer')
                ->after('person_id');

            $table->enum('status', ['pending', 'active', 'deactivated'])
                ->default('pending')
                ->after('role');

            $table->text('registration_note')
                ->nullable()
                ->after('status')
                ->comment('User explains who they are during registration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['person_id']);
            $table->dropColumn(['person_id', 'role', 'status', 'registration_note']);
        });
    }
};
