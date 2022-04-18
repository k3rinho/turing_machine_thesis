<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // columns after email, company, uuid
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop cols
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
}
