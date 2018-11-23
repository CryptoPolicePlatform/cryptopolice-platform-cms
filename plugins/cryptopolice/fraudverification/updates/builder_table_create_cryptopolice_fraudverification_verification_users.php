<?php namespace CryptoPolice\FraudVerification\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopoliceFraudverificationVerificationUsers extends Migration
{
    public function up()
    {

       Schema::dropIfExists('cryptopolice_fraudverification_verification_users');

        Schema::create('cryptopolice_fraudverification_verification_users', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('application_id')->nullable();
            $table->integer('verdict_id')->nullable();
            $table->smallInteger('level_id');
            $table->boolean('status');
            $table->smallInteger('type');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cryptopolice_fraudverification_verification_users');
    }
}
