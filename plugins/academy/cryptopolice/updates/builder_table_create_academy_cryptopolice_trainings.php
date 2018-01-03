<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateAcademyCryptopoliceTrainings extends Migration
{
    public function up()
    {
        Schema::create('academy_cryptopolice_trainings', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('title');
            $table->text('slug');
            $table->text('description');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('user_id')->default(0);
            $table->integer('status')->default(0);
            $table->integer('likes')->default(0);
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('academy_cryptopolice_trainings');
    }
}
