<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeUsernameRequiredOnUsersTable extends Migration
{
    public function up()
    {
        // Fill any existing NULL usernames before making NOT NULL
        DB::table('users')->whereNull('username')->orderBy('id')->each(function ($user) {
            $base     = preg_replace('/[^a-z0-9_]/', '_', strtolower(explode('@', $user->email)[0]));
            $username = $base;
            $suffix   = 1;
            while (DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $base . '_' . $suffix++;
            }
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        });

        DB::statement('ALTER TABLE users MODIFY COLUMN username VARCHAR(50) NOT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE users MODIFY COLUMN username VARCHAR(50) NULL');
    }
}
