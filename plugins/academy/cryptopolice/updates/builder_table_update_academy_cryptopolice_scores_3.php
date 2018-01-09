<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceScores3 extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_scores', function($table)
        {
            $table->renameColumn('scores', 'try');
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_scores', function($table)
        {
            $table->renameColumn('try', 'scores');
        });
    }
}
