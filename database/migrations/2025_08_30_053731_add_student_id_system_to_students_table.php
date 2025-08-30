<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentIdSystemToStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_id')->unique()->after('id');
            $table->string('library_card_number')->unique()->after('student_id');
            $table->date('enrollment_date')->default(now())->after('library_card_number');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('enrollment_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['student_id', 'library_card_number', 'enrollment_date', 'status']);
        });
    }
}
