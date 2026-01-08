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
        Schema::table('table_sessions', function (Blueprint $table) {
            $table->string('session_type')->nullable()->after('table_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_sessions', function (Blueprint $table) {
        // Remove the added column
        $table->dropColumn('session_type');

        // Revert table_id to NOT NULL
        $table->string('table_id')->nullable(false)->change();
        });
    }
};
