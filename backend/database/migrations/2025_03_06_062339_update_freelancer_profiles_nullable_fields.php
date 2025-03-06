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
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->string('skills')->nullable()->change();
            $table->text('experience')->nullable()->change();
            $table->text('portfolio')->nullable()->change();
            $table->decimal('hourly_rate', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->string('skills')->nullable(false)->change();
            $table->text('experience')->nullable(false)->change();
            $table->text('portfolio')->nullable(false)->change();
            $table->decimal('hourly_rate', 8, 2)->nullable(false)->change();
        });
    }
};
