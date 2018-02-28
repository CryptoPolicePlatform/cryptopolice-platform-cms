<?php namespace CryptoPolice\Platform\Updates;

use DB;
use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateCryptopolicePlatformCommunityPosts extends Migration
{

    /*
     * CryptoPolice Platform Tables
     */

    public function up()
    {

        if (!Schema::hasTable('cryptopolice_platform_community_posts')) {

            Schema::create('cryptopolice_platform_community_posts', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('slug', 255)->nullable();
                $table->string('post_title', 255)->nullable();
                $table->text('post_description', 10000)->nullable();
                $table->integer('user_id')->nullable();
                $table->boolean('status')->default(0);
                $table->boolean('pin')->default(0);
                $table->integer('comment_count')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_platform_community_post_views')) {

            Schema::create('cryptopolice_platform_community_post_views', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('session_id', 40)->nullable();
                $table->integer('post_id')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_platform_notifications')) {

            Schema::create('cryptopolice_platform_notifications', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title', 255)->nullable();
                $table->text('description')->nullable();
                $table->boolean('status')->default(1);
                $table->integer('user_id')->nullable();
                $table->timestamp('announcement_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }


        if (!Schema::hasTable('cryptopolice_platform_users_notifications')) {

            Schema::create('cryptopolice_platform_users_notifications', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->integer('user_id')->nullable();
                $table->integer('notification_id')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
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

        if (!Schema::hasTable('cryptopolice_platform_scams')) {

            Schema::create('cryptopolice_platform_scams', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->boolean('status')->default(0);
                $table->string('title', 255)->nullable();
                $table->string('description', 1000)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down() {

    }
}
