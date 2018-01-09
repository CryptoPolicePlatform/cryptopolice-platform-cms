<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceExams4 extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_exams', function($table)
        {
            $table->integer('retake_time')->default(1)->change();
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_exams', function($table)
        {
            $table->integer('retake_time')->default(0)->change();
        });
    }
}
