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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('country');
            $table->string('province');
            $table->string('description')->nullable();
            $table->Integer('rooms')->default(1);
            $table->decimal('price' , 10 ,2);
            $table->Integer('ratings_count')->default(0);
            $table->Integer('ratings_sum')->default(0);
            $table->decimal('rating' ,3 , 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
