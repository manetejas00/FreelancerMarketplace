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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade'); // foreign key to jobs
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // foreign key to users (freelancer)
            $table->decimal('rate', 8, 2); // Proposed rate
            $table->text('cover_letter'); // Cover letter
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending'); // Status of the bid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
