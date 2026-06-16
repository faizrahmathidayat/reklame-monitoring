<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSpkIdToReklamesAndMigrateData extends Migration
{
    public function up()
    {
        // Step 1: Add spk_id (nullable during migration)
        Schema::table('reklames', function (Blueprint $table) {
            $table->unsignedBigInteger('spk_id')->nullable()->after('id');
            $table->foreign('spk_id')->references('id')->on('spks')->onDelete('cascade');
        });

        // Step 2: Data migration — original schema had no_spk unique, so 1 reklame = 1 SPK
        $reklames = DB::table('reklames')->get();
        foreach ($reklames as $r) {
            $spkId = DB::table('spks')->insertGetId([
                'no_spk'              => $r->no_spk,
                'tgl_spk'             => $r->tgl_spk,
                'deadline'            => $r->deadline,
                'wilayah_id'          => $r->wilayah_id,
                'brand_id'            => $r->brand_id,
                'cabang_id'           => $r->cabang_id,
                'pic_id'              => $r->pic_id,
                'mulai_tanggal_input' => $r->mulai_tanggal_input,
                'keterangan'          => $r->keterangan,
                'note'                => $r->note,
                'created_by'          => $r->created_by,
                'updated_by'          => $r->updated_by,
                'created_at'          => $r->created_at ?? now(),
                'updated_at'          => $r->updated_at ?? now(),
                'deleted_at'          => $r->deleted_at,
            ]);
            DB::table('reklames')->where('id', $r->id)->update(['spk_id' => $spkId]);
        }

        // Step 3: no_spk no longer needs to be unique in reklames
        // (uniqueness is now managed at the spks level)
        Schema::table('reklames', function (Blueprint $table) {
            $table->dropUnique('reklames_no_spk_unique');
        });
    }

    public function down()
    {
        Schema::table('reklames', function (Blueprint $table) {
            $table->unique('no_spk');
        });

        Schema::table('reklames', function (Blueprint $table) {
            $table->dropForeign(['spk_id']);
            $table->dropColumn('spk_id');
        });
    }
}
