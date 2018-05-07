<?php namespace cryptopolice\airdrop\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopoliceAirdropAirdrop extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('cryptopolice_airdrop_airdrop')) {

            Schema::create('cryptopolice_airdrop_airdrop', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->boolean('status')->default(1);
                $table->text('fields')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }
    }
    
    public function down()
    {
    }
}
