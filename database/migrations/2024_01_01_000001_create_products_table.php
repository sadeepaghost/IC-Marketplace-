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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // --- Core Identification ---
            $table->string('part_number', 100)->unique()->index();
            $table->string('manufacturer', 150);
            $table->string('description', 500);
            $table->string('category', 100)->index();          // e.g. "Op-Amp", "Microcontroller"
            $table->string('series', 100)->nullable();          // e.g. "LM7xx Series"

            // --- Inventory & Pricing ---
            $table->enum('stock_status', ['in_stock', 'low_stock', 'out_of_stock', 'discontinued'])
                  ->default('out_of_stock')
                  ->index();
            $table->unsignedInteger('quantity_available')->default(0);
            $table->unsignedInteger('minimum_order_quantity')->default(1);
            $table->decimal('unit_price_usd', 10, 4)->nullable();
            $table->decimal('bulk_price_usd', 10, 4)->nullable();    // price break at bulk_qty
            $table->unsignedInteger('bulk_qty_threshold')->nullable();

            // --- Technical Specifications (IC-specific) ---
            $table->string('package_type', 50)->nullable();          // e.g. "DIP-8", "SOIC-8", "QFN-32"
            $table->string('supply_voltage_min', 20)->nullable();    // e.g. "3.3V"
            $table->string('supply_voltage_max', 20)->nullable();    // e.g. "18V"
            $table->string('operating_temp_min', 20)->nullable();    // e.g. "-40°C"
            $table->string('operating_temp_max', 20)->nullable();    // e.g. "125°C"
            $table->string('frequency', 50)->nullable();             // e.g. "1MHz", "240MHz"
            $table->string('output_current', 50)->nullable();        // e.g. "±25mA"
            $table->string('input_offset_voltage', 50)->nullable();  // relevant for op-amps
            $table->string('bandwidth', 50)->nullable();
            $table->unsignedTinyInteger('pin_count')->nullable();
            $table->string('interface', 100)->nullable();            // e.g. "I2C, SPI, UART"
            $table->string('flash_memory', 50)->nullable();          // e.g. "256KB"
            $table->string('ram', 50)->nullable();                   // e.g. "32KB"
            $table->json('additional_specs')->nullable();            // flexible key-value for extra specs

            // --- Compliance & Media ---
            $table->boolean('rohs_compliant')->default(true);
            $table->boolean('reach_compliant')->default(true);
            $table->string('datasheet_url', 2048)->nullable();
            $table->string('image_url', 2048)->nullable();

            // --- Sourcing & Logistics ---
            $table->string('lead_time', 50)->nullable();             // e.g. "8 weeks"
            $table->string('origin_country', 100)->nullable();
            $table->string('eccn', 20)->nullable();                  // Export control

            // --- Search Optimization ---
            $table->text('search_tags')->nullable();                 // comma-separated tags
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
