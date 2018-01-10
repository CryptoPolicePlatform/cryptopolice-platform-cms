<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceScores extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_scores', function($table)
        {
            $table->integer('scores')->default(0)->change();
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_scores', function($table)
        {
            $table->integer('scores')->default(null)->change();
        });
    }
}
