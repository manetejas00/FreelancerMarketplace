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
            $table->string('company_name')->nullable()->after('user_id');
            $table->text('project_details')->nullable()->after('company_name');
            $table->integer('working_developers_count')->default(0)->after('project_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('freelancer_profiles', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'project_details', 'working_developers_count']);
        });
    }
};
