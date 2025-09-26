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
        Schema::create('tikets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('category');
            $table->enum('status', ['menunggu', 'selesai', 'proses'])->default('menunggu');
            $table->text('description');
            $table->foreignId('cs_menangani')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot table untuk teknisi
        Schema::create('tiket_teknisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tikets')->onDelete('cascade');
            $table->foreignId('teknisi_id')->constrained('users')->onDelete('cascade'); // teknisi dari users
            $table->timestamps();
        });

    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiket_teknisi');
        Schema::dropIfExists('tikets');
    }
};
