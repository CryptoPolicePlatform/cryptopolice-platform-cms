<?php namespace CryptoPolice\Bitcointalk\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateContentsTable extends Migration
{
    public function up()
    {
        Schema::create('cryptopolice_bitcointalk_contents', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('content')->nullable();
            $table->text('content_raw')->nullable();
            $table->string('user_nick')->nullable()->index();
            $table->string('user_profil')->nullable();
            $table->string('publication_date')->nullable();
            $table->string('meta')->nullable();
            $table->string('hash', 40)->nullable()->index();
            $table->integer('contentable_id')->index();
            $table->string('contentable_type')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cryptopolice_bitcointalk_contents');
    }
}
