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
            $table->dropColumn(['name', 'email', 'email_verified_at', 'remember_token','created_at' ,'updated_at']);


            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('access_pin');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
                // Drop new columns
                $table->dropForeign(['role_id']);
                $table->dropColumn(['user_id', 'role_id', 'first_name', 'last_name', 'access_pin', 'password', 'created_at']);

                // Re-add old columns
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('remember_token', 100)->nullable();
                $table->timestamps(); // adds created_at and updated_at
            });
    }
};
