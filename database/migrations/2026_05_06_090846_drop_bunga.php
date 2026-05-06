<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom bunga_persen, jenis_bunga, periode_bunga dari tabel modals
     * dan kolom nominal_pokok, nominal_bunga dari tabel modal_cicilans
     * karena skema bunga sekarang memakai nominal total (Rp), bukan persentase.
     */
    public function up(): void
    {
        // ── Tabel modals ─────────────────────────────────────────────
        Schema::table('modals', function (Blueprint $table) {
            $table->dropColumn([
                'bunga_persen',   // diganti oleh total_bunga (Rp) yang sudah ada
                'jenis_bunga',    // flat / efektif / anuitas — tidak relevan lagi
                'periode_bunga',  // bulanan / harian / dst — tidak relevan lagi
            ]);
        });

        // ── Tabel modal_cicilans ──────────────────────────────────────
        Schema::table('modal_cicilans', function (Blueprint $table) {
            $table->dropColumn([
                'nominal_pokok',  // detail pokok per cicilan — tidak ditampilkan lagi
                'nominal_bunga',  // detail bunga per cicilan — tidak ditampilkan lagi
            ]);
        });
    }

    /**
     * Rollback: kembalikan semua kolom yang dihapus.
     * Nilai default 0 / null dipakai karena data lama sudah tidak tersedia.
     */
    public function down(): void
    {
        // ── Tabel modals ─────────────────────────────────────────────
        Schema::table('modals', function (Blueprint $table) {
            $table->decimal('bunga_persen', 10, 4)->default(0)->after('nominal_pencairan');
            $table->enum('jenis_bunga', ['flat', 'efektif', 'anuitas'])->default('flat')->after('bunga_persen');
            $table->enum('periode_bunga', ['harian', 'mingguan', 'bulanan', 'tahunan'])->default('bulanan')->after('jenis_bunga');
        });

        // ── Tabel modal_cicilans ──────────────────────────────────────
        Schema::table('modal_cicilans', function (Blueprint $table) {
            $table->decimal('nominal_pokok', 15, 2)->default(0)->after('tanggal_jatuh_tempo');
            $table->decimal('nominal_bunga', 15, 2)->default(0)->after('nominal_pokok');
        });
    }
};
