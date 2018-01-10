<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceExams extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_exams', function($table)
        {
            $table->dateTime('retake_time');
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_exams', function($table)
        {
            $table->dropColumn('retake_time');
        });
    }
}
