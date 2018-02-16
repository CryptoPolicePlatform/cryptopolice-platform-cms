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

        // Update users nicknames

        $rows = DB::table('users')->get(['id', 'nickname', 'email']);

        foreach ($rows as $row) {

            $nickname = explode("@", $row->email);

            if($row->nickname == null) {
                DB::table('users')->where('id', $row->id)->update([
                    'nickname' => $nickname[0]
                ]);
            }
        }

        if (!Schema::hasTable('cryptopolice_platform_community_posts')) {

            Schema::create('cryptopolice_platform_community_posts', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('slug', 255)->nullable();
                $table->string('post_title', 255)->nullable();
                $table->string('post_image', 255)->nullable();
                $table->text('post_description', 10000)->nullable();
                $table->integer('user_id')->nullable();
                $table->boolean('status')->default(0);
                $table->boolean('pin')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_platform_community_post_views')) {

            Schema::create('cryptopolice_platform_community_post_views', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->integer('post_id')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_platform_notifications')) {

            Schema::create('cryptopolice_platform_notifications', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title', 255);
                $table->text('description');
                $table->boolean('status')->default(1);
                $table->timestamp('announcement_at')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }


        if (!Schema::hasTable('cryptopolice_platform_users_notifications')) {

            Schema::create('cryptopolice_platform_users_notifications', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->integer('notification_id');
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
    }

    public function down()
    {

    }
}
