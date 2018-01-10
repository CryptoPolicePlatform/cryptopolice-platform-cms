<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceFinalExamScore2 extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_final_exam_score', function($table)
        {
            $table->text('try')->nullable(false)->unsigned(false)->default('0')->change();
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_final_exam_score', function($table)
        {
            $table->integer('try')->nullable(false)->unsigned(false)->default(0)->change();
        });
    }
}
