<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceExams2 extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_exams', function($table)
        {
            $table->integer('retake_time')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_exams', function($table)
        {
            $table->dateTime('retake_time')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
