<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpksTable extends Migration
{
    public function up()
    {
        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->date('tgl_spk');
            $table->date('deadline')->nullable();
            $table->string('no_spk', 100)->unique();
            $table->foreignId('wilayah_id')->constrained('wilayahs');
            $table->foreignId('brand_id')->constrained('brands');
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->nullOnDelete();
            $table->foreignId('pic_id')->nullable()->constrained('pics')->nullOnDelete();
            $table->date('mulai_tanggal_input')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tgl_spk');
            $table->index('wilayah_id');
            $table->index('brand_id');
            $table->index(['tgl_spk', 'wilayah_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('spks');
    }
}
