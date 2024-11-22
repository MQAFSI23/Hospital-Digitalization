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
        Schema::create('log_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->integer('jumlah');
            $table->enum('status', ['terisi', 'terjual']);
            $table->timestamp('tanggal_log')->useCurrent();
            $table->timestamps();
        });;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_obat');
    }
};
