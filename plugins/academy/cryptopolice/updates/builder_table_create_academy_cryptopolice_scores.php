<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAcademyCryptopoliceScores extends Migration
{
    public function up()
    {
        Schema::create('academy_cryptopolice_scores', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('scores');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('exam_id');
            $table->integer('question_num');
            $table->integer('answer_num');
            $table->boolean('is_correct');
            $table->integer('user_id');
            $table->integer('try')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('academy_cryptopolice_scores');
    }
}
