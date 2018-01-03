<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAcademyCryptopoliceFinalExamScore extends Migration
{
    public function up()
    {
        Schema::create('academy_cryptopolice_final_exam_score', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id');
            $table->integer('exam_id');
            $table->integer('score');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->dateTime('completed_at');
            $table->boolean('complete_status')->default(0);
            $table->integer('try')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('academy_cryptopolice_final_exam_score');
    }
}
