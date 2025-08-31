<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBooksTableForeignKeyColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('books', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['auther_id']);
            
            // Rename the column
            $table->renameColumn('auther_id', 'author_id');
            
            // Add the foreign key constraint back with the new column name
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
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
            // Drop the foreign key constraint
            $table->dropForeign(['author_id']);
            
            // Rename the column back
            $table->renameColumn('author_id', 'auther_id');
            
            // Add the foreign key constraint back with the old column name
            $table->foreign('auther_id')->references('id')->on('authers')->onDelete('cascade');
        });
    }
}
