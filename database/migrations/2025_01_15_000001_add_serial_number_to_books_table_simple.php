<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSerialNumberToBooksTableSimple extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('books', 'serial_number')) {
            Schema::table('books', function (Blueprint $table) {
                $table->unsignedInteger('serial_number')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('books', 'serial_number')) {
            Schema::table('books', function (Blueprint $table) {
                $table->dropColumn('serial_number');
            });
        }
    }
}
