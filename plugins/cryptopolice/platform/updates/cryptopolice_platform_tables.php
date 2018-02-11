<?php namespace CryptoPolice\Platform\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopolicePlatformCommunityPosts extends Migration
{
    public function up()
    {

        if (!Schema::hasTable('cryptopolice_platform_community_posts')) {

            Schema::create('cryptopolice_platform_community_posts', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('post_title', 255)->nullable();
                $table->string('post_image', 255)->nullable();
                $table->text('post_description', 10000)->nullable();
                $table->integer('user_id')->nullable();
                $table->integer('comment_id')->nullable();
                $table->boolean('status')->default(0);
                $table->boolean('pin')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_platform_community_comment')) {

            Schema::create('cryptopolice_platform_community_comment', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('post_id')->nullable();
                $table->integer('parent_id')->default(0);
                $table->string('description', 1000)->nullable();
                $table->integer('user_id')->nullable();
                $table->boolean('status')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('cryptopolice_platform_community_posts');
        Schema::dropIfExists('cryptopolice_platform_community_comment');
    }
}
