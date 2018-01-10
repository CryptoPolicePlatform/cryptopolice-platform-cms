<?php namespace Academy\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateAcademyCryptopoliceScores2 extends Migration
{
    public function up()
    {
        Schema::table('academy_cryptopolice_scores', function($table)
        {
            $table->dropColumn('try');
        });
    }
    
    public function down()
    {
        Schema::table('academy_cryptopolice_scores', function($table)
        {
            $table->integer('try')->default(0);
        });
    }
}
