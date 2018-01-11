<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceTrainings extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_trainings', function($table)
        {
            $table->string('title', 255)->nullable(false)->unsigned(false)->default(null)->change();
            $table->string('slug', 255)->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_trainings', function($table)
        {
            $table->text('title')->nullable(false)->unsigned(false)->default(null)->change();
            $table->text('slug')->nullable(false)->unsigned(false)->default(null)->change();
        });
    }
}
