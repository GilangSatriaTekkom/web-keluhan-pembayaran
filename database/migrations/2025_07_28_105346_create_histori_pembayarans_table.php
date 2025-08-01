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
        Schema::create('histori_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('metode_pembayaran');
            $table->string('status_pembayaran');
            $table->integer('jumlah_dibayar');
            $table->date('tanggal_pembayaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('histori_pembayarans');
    }
};
