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
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->char('nip', 18);
            $table->date('tanggal_lahir');
            $table->string('pangkat_golongan');
            $table->string('jabatan');
            $table->text('alamat');
            $table->string('foto')->nullable();
            $table->string('cv')->nullable();;
            $table->boolean('status')->default(true);
            $table->foreignIdFor(\App\Models\Divisi::class, 'divisi_id')->constrained('divisi', 'id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
