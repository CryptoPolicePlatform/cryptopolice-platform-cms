<?php namespace CryptoPolice\FraudVerification\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateCryptopoliceFraudverificationBecomeToOfficer extends Migration
{
    public function up()
    {
        Schema::table('cryptopolice_fraudverification_become_to_officer', function($table)
        {
            $table->string('role', 255);
            $table->text('comment');
            $table->integer('user_id')->default(0)->change();
        });
    }
    
    public function down()
    {
        Schema::table('cryptopolice_fraudverification_become_to_officer', function($table)
        {
            $table->dropColumn('role');
            $table->dropColumn('comment');
            $table->integer('user_id')->default(NULL)->change();
        });
    }
}
