<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderStatusNameOccupiedColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('occupied')->default(false);
        });

        Schema::table('assigned_lecturers', function (Blueprint $table) {
            $table->unsignedTinyInteger('order');
        });
        Schema::table('course_program_details', function (Blueprint $table) {
            $table->unsignedTinyInteger('order');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->string('name')->after('id')->nullable();
            $table->enum('status', ['inactive', 'current', 'future']);
        });
        Schema::table('student_groups', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->boolean('active')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('occupied');
        });

        Schema::table('assigned_lecturers', function (Blueprint $table) {
            $table->dropColumn('order');
        });
        Schema::table('course_program_details', function (Blueprint $table) {
            $table->dropColumn('order');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('status');
        });
        Schema::table('student_groups', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('active');
        });
    }
}
