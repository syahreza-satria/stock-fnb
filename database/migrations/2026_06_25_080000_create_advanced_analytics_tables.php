<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Add cost price to ingredients (to calculate Food Cost)
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('cost_price', 12, 2)->default(0.00)->after('stock');
        });

        // 2. Add selling price to recipes (to calculate margin)
        Schema::table('recipes', function (Blueprint $table) {
            $table->decimal('selling_price', 12, 2)->default(0.00)->after('name');
        });

        // 3. Create Stock Takes table (for variance report: theoretical vs actual)
        Schema::create('stock_takes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->foreignId('ingredient_id')->constrained()->onDelete('cascade');
            $table->decimal('theoretical_stock', 12, 2);
            $table->decimal('actual_stock', 12, 2);
            $table->decimal('variance', 12, 2);
            $table->string('notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_takes');

        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn('selling_price');
        });

        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });
    }
};
