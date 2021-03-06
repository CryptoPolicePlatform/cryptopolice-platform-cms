<?php namespace CryptoPolice\FraudVerification\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopoliceFraudverificationApplicationTypes extends Migration
{
    public function up()
    {
        //Schema::dropIfExists('cryptopolice_fraudverification_application_types');

        Schema::create('cryptopolice_fraudverification_application_types', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('type');
            $table->text('description');
            $table->integer('category_id')->nullable();
            $table->smallInteger('order');
            $table->boolean('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cryptopolice_fraudverification_application_types');
    }
}
