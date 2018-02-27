<?php namespace CryptoPolice\Bitcointalk\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateTopiksTable extends Migration
{
    public function up()
    {
        Schema::create('cryptopolice_bitcointalk_topics', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('url')->unique();
            $table->string('title')->nullable();
            $table->integer('bitcointalk_id')->unique()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cryptopolice_bitcointalk_topics');
    }
}
