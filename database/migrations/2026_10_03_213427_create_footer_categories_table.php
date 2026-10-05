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
        Schema::create('footer_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category1')->default(0)->nullable();
            $table->bigInteger('category2')->default(0)->nullable();
            $table->bigInteger('category3')->default(0)->nullable();
            $table->bigInteger('category4')->default(0)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_categories');
    }
};
