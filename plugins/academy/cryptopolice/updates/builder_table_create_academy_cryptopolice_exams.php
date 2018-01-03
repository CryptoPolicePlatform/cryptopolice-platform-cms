<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAcademyCryptopoliceExams extends Migration
{
    public function up()
    {
        Schema::create('academy_cryptopolice_exams', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('exam_title', 255);
            $table->string('exam_description', 255);
            $table->string('exam_slug', 255);
            $table->text('question');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('timer');
            $table->integer('s_score');
            $table->integer('status');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('academy_cryptopolice_exams');
    }
}
