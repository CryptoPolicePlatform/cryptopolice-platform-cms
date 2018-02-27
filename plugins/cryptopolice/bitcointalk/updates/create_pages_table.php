<?php namespace CryptoPolice\Bitcointalk\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePagesTable extends Migration
{
    public function up()
    {
        Schema::create('cryptopolice_bitcointalk_pages', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->longText('html');
            $table->string('full_url');
            $table->string('title')->nullable();
            $table->text('meta')->nullable();
            $table->integer('pageable_id');
            $table->string('pageable_type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cryptopolice_bitcointalk_pages');
    }
}
