<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('freelancer_id')->constrained('users')->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('job_applications');
    }
};
