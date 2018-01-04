<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceFinalExamScore extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_final_exam_score', function($table)
        {
            $table->integer('user_id')->default(0)->change();
            $table->integer('exam_id')->default(0)->change();
            $table->integer('score')->default(0)->change();
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_final_exam_score', function($table)
        {
            $table->integer('user_id')->default(null)->change();
            $table->integer('exam_id')->default(null)->change();
            $table->integer('score')->default(null)->change();
        });
    }
}
