<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchToClassroomsAndStudentGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->unsignedSmallInteger('batch');
        });
        Schema::table('student_groups', function (Blueprint $table) {
            $table->unsignedSmallInteger('batch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('batch');
        });
        Schema::table('student_groups', function (Blueprint $table) {
            $table->dropColumn('batch');
        });
    }
}
