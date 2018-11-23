<?php namespace CryptoPolice\FraudVerification\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopoliceFraudverificationVerificationLevels extends Migration
{
    public function up()
    {
        Schema::dropIfExists('cryptopolice_fraudverification_verification_levels');

        Schema::create('cryptopolice_fraudverification_verification_levels', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('level');
            $table->text('description');
            $table->smallInteger('officer_count');
            $table->smallInteger('verification_order');
            $table->boolean('status');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cryptopolice_fraudverification_verification_levels');
    }
}
