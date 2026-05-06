<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modals', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjaman')->unique();
            $table->string('nama_pemberi_pinjaman');
            $table->enum('jenis_pinjaman', ['bank', 'koperasi', 'perorangan', 'lembaga_keuangan', 'lainnya']);
            $table->decimal('jumlah_pinjaman', 18, 2);      // Nominal ajuan pinjaman
            $table->decimal('nominal_pencairan', 18, 2);    // Nominal yang benar-benar cair
            $table->decimal('bunga_persen', 8, 4);          // % bunga per periode
            $table->enum('jenis_bunga', ['flat', 'efektif', 'anuitas']);
            $table->enum('periode_bunga', ['harian', 'mingguan', 'bulanan', 'tahunan']);
            $table->integer('tenor');                        // Jumlah cicilan
            $table->enum('satuan_tenor', ['hari', 'minggu', 'bulan', 'tahun']);
            $table->decimal('total_bunga', 18, 2);          // Total bunga keseluruhan
            $table->decimal('total_kewajiban', 18, 2);      // Total yang harus dibayar (pokok + bunga)
            $table->decimal('cicilan_per_periode', 18, 2);  // Nominal cicilan per periode
            $table->date('tanggal_pinjaman');
            $table->date('tanggal_pencairan');
            $table->date('tanggal_jatuh_tempo');
            $table->integer('cicilan_ke')->default(0);       // Progress cicilan
            $table->decimal('total_terbayar', 18, 2)->default(0);
            $table->decimal('sisa_kewajiban', 18, 2);
            $table->enum('status', ['aktif', 'lunas', 'macet', 'restrukturisasi'])->default('aktif');
            $table->text('keterangan')->nullable();
            $table->boolean('sudah_dicairkan')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('modal_cicilans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modal_id')->constrained('modals')->onDelete('cascade');
            $table->integer('cicilan_ke');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('nominal_pokok', 18, 2);
            $table->decimal('nominal_bunga', 18, 2);
            $table->decimal('nominal_cicilan', 18, 2);      // pokok + bunga
            $table->decimal('denda', 18, 2)->default(0);
            $table->decimal('total_bayar', 18, 2)->default(0);
            $table->enum('status', ['belum_bayar', 'sudah_bayar', 'terlambat', 'sebagian'])->default('belum_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modal_cicilans');
        Schema::dropIfExists('modals');
    }
};
