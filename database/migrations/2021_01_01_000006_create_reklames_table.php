<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReklamesTable extends Migration
{
    public function up()
    {
        Schema::create('reklames', function (Blueprint $table) {
            $table->id();

            // ── Identitas SPK ─────────────────────────────────────────────
            $table->date('tgl_spk');
            $table->date('deadline');
            $table->string('no_spk', 100)->unique();

            // ── Relasi Master ─────────────────────────────────────────────
            $table->foreignId('wilayah_id')->constrained('wilayahs');
            $table->foreignId('toko_id')->constrained('tokos');
            $table->foreignId('brand_id')->constrained('brands');
            $table->foreignId('cabang_id')->constrained('cabangs');
            $table->foreignId('pic_id')->constrained('pics');

            // kode_toko disimpan sebagai denormalized copy untuk kemudahan
            // pencarian/report tanpa selalu join tabel tokos
            $table->string('kode_toko', 50);

            // ── Detail Reklame ────────────────────────────────────────────
            $table->string('ukuran_reklame', 100);
            $table->date('tanggal_awal');
            $table->date('tanggal_awal_toko_baru')->nullable();
            $table->date('tanggal_akhir_toko_baru')->nullable();

            // ── Field Finance (diisi/diupdate oleh role Finance) ──────────
            $table->decimal('nominal', 18, 2)->nullable();         // Nominal SKPD Baru
            $table->date('tgl_terbit_skpd_baru')->nullable();
            $table->string('nomor_bayar', 100)->nullable();
            $table->date('jatuh_tempo')->nullable();

            // ── Tracking Input & Progress ─────────────────────────────────
            $table->date('mulai_tanggal_input');
            $table->date('tanggal_update')->nullable();

            // ── Status Workflow ───────────────────────────────────────────
            $table->enum('status', [
                'CANCEL',
                'INPUT WEB',
                'INVOICE TERKIRIM',
                'MENUNGGU IPR',
                'PEMBERKASAN',
                'PETUGAS PELAYANAN',
                'PROSES INVOICE',
                'PROSES PEMBAYARAN',
                'SELESAI',
                'SELESAI INV TERKIRIM',
                'SELESAI TERBIT SKPD',
            ])->default('INPUT WEB');

            // ── Penolakan / Revisi ────────────────────────────────────────
            $table->text('di_tolak')->nullable();                  // Nomor Registrasi jika ditolak
            $table->date('tgl_pengajuan_ulang')->nullable();

            // ── Catatan ───────────────────────────────────────────────────
            $table->text('keterangan')->nullable();
            $table->text('note')->nullable();

            // ── Audit Trail ───────────────────────────────────────────────
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // ── Indexes untuk performa query report & filter ──────────────
            $table->index('tgl_spk');
            $table->index('status');
            $table->index('wilayah_id');
            $table->index('toko_id');
            $table->index('brand_id');
            $table->index(['wilayah_id', 'status']);
            $table->index(['tgl_spk', 'wilayah_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reklames');
    }
}
