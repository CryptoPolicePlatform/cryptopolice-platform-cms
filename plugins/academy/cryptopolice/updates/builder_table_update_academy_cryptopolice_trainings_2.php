<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceTrainings2 extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_trainings', function($table)
        {
            $table->string('title', 255)->nullable()->change();
            $table->string('slug', 255)->nullable()->change();
            $table->text('description')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_trainings', function($table)
        {
            $table->string('title', 255)->nullable(false)->change();
            $table->string('slug', 255)->nullable(false)->change();
            $table->text('description')->nullable(false)->change();
        });
    }
}
