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
        Schema::create('documents_request', function (Blueprint $table) {
            $table->id();
            $table->string('number_registration')->unique();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verificator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'revision',
                'approved',
                'rejected',
            ])->default('submitted');

            $table->text('verificator_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index(['status', 'applicant_id']);
            $table->index(['status', 'verificator_id']);
            $table->index('created_at');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_request');
    }
};
