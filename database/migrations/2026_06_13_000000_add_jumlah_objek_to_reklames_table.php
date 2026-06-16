<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJumlahObjekToReklamesTable extends Migration
{
    public function up()
    {
        Schema::table('reklames', function (Blueprint $table) {
            $table->unsignedSmallInteger('jumlah_objek')->nullable()->default(1)->after('ukuran_reklame');
        });
    }

    public function down()
    {
        Schema::table('reklames', function (Blueprint $table) {
            $table->dropColumn('jumlah_objek');
        });
    }
}
