<?php namespace CryptoPolice\FraudVerification\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopoliceFraudverificationApplication extends Migration
{
    public function up()
    {
        Schema::dropIfExists('cryptopolice_fraudverification_application');

        Schema::create('cryptopolice_fraudverification_application', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id');
            $table->string('domain', 255);
            $table->text('task');
            $table->boolean('status');
            $table->smallInteger('type_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cryptopolice_fraudverification_application');
    }
}
