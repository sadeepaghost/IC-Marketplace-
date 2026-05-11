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
        Schema::create('sourcing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 30)->unique();       // e.g. "SR-2024-00042"

            // --- Requested Part ---
            $table->string('part_number', 100)->index();
            $table->string('manufacturer', 150)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity_required');
            $table->string('target_price_usd', 50)->nullable();     // customer's budget
            $table->date('required_by_date')->nullable();

            // --- Customer Contact ---
            $table->string('customer_name', 200);
            $table->string('customer_email', 254)->index();
            $table->string('customer_phone', 30)->nullable();
            $table->string('company_name', 200)->nullable();
            $table->string('country', 100)->nullable();
            $table->text('customer_notes')->nullable();

            // --- AI Suggestion Metadata ---
            $table->json('suggested_alternatives')->nullable();     // JSON array of suggested part numbers
            $table->string('ai_confidence_score', 10)->nullable();  // e.g. "0.87"
            $table->text('ai_reasoning')->nullable();

            // --- Workflow Status ---
            $table->enum('status', [
                'pending',          // just received
                'ai_processing',    // AI is searching for alternatives
                'supplier_queried', // sent to suppliers
                'quote_received',   // supplier responded
                'quote_sent',       // we sent quote to customer
                'accepted',         // customer accepted
                'rejected',         // customer rejected
                'expired',          // no response in 30 days
                'fulfilled',        // order complete
            ])->default('pending')->index();

            $table->text('admin_notes')->nullable();
            $table->string('assigned_to', 200)->nullable();         // staff member

            // --- Notifications ---
            $table->boolean('admin_notified')->default(false);
            $table->timestamp('admin_notified_at')->nullable();
            $table->boolean('customer_quote_sent')->default(false);
            $table->timestamp('customer_quote_sent_at')->nullable();

            // --- Session / Attribution ---
            $table->string('ip_address', 45)->nullable();           // supports IPv6
            $table->string('user_agent', 500)->nullable();
            $table->string('session_id', 100)->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate requests from the same customer for the same part
            $table->index(['customer_email', 'part_number', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sourcing_requests');
    }
};
