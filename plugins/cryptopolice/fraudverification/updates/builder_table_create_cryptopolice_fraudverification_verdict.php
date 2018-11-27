<?php namespace CryptoPolice\FraudVerification\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopoliceFraudverificationVerdict extends Migration
{
    public function up()
    {

        //Schema::dropIfExists('cryptopolice_fraudverification_verdict');

        Schema::create('cryptopolice_fraudverification_verdict', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('application_id');
            $table->integer('verdict_type_id');
            $table->integer('verification_id');
            $table->integer('parent_id')->nullable();
            $table->text('comment');
            $table->boolean('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cryptopolice_fraudverification_verdict');
    }
}
