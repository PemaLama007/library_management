<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryToBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->integer('total_copies')->default(1)->after('status');
            $table->integer('available_copies')->default(1)->after('total_copies');
            $table->string('isbn')->nullable()->after('name');
            $table->text('description')->nullable()->after('isbn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['total_copies', 'available_copies', 'isbn', 'description']);
        });
    }
}
