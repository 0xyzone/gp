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
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['add', 'subtract', 'adjustment'])->default('add');
            $table->integer('quantity'); // Positive number of units
            $table->date('entry_date')->default(now()->toDateString());
            $table->string('reference_number')->nullable(); // e.g. Batch #, Supplier Invoice #
            $table->text('notes')->nullable(); // e.g. "Morning expo batch restock", "Mistake correction"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_entries');
    }
};
