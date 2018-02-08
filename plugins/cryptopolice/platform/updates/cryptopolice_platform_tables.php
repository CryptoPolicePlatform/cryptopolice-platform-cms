<?php namespace CryptoPolice\Platform\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopolicePlatformCommunityPosts extends Migration
{
    public function up()
    {
        Schema::create('cryptopolice_platform_community_posts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('post_title', 255)->nullable();
            $table->string('post_image', 255)->nullable();
            $table->text('post_description')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('comment_id')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('cryptopolice_platform_community_posts');
    }
}
