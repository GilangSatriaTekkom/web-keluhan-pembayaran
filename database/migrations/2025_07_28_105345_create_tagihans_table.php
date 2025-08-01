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
       Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('langganan_id')
                ->nullable()
                ->constrained('langganans')
                ->nullOnDelete(); // GANTI dari manual foreign
            $table->string('status_pembayaran');
            $table->string('metode_pembayaran')->nullable();
            $table->integer('jumlah_tagihan');
            $table->date('tgl_jatuh_tempo');
            $table->string('periode_tagihan');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
