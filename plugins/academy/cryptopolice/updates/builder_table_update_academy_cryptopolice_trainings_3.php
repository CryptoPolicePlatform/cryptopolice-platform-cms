<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceTrainings3 extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_trainings', function($table)
        {
            $table->string('title', 255)->nullable()->change();
            $table->string('slug', 255)->nullable()->change();
            $table->string('description', 255)->nullable()->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_trainings', function($table)
        {
            $table->string('title', 255)->nullable(false)->change();
            $table->string('slug', 255)->nullable(false)->change();
            $table->text('description')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
