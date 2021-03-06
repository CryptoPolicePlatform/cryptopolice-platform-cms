<?php namespace CryptoPolice\Academy\Updates;

use DB;
use Schema;
use October\Rain\Database\Updates\Migration;

class CryptoPolicePlatformAcademyTables extends Migration
{

    /*
     * CryptoPolice Academy Tables
     */

    public function up()
    {

        // Social Networks
        if (!Schema::hasColumns('users', ['eth_address'])) {
            Schema::table('users', function ($table) {
                $table->string('eth_address', 42)->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['nickname'])) {
            Schema::table('users', function ($table) {
                $table->string('nickname', 150)->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['activation_at'])) {
            Schema::table('users', function ($table) {
                $table->timestamp('activation_at')->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['twitter_link'])) {
            Schema::table('users', function ($table) {
                $table->string('twitter_link', 255)->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['facebook_link'])) {
            Schema::table('users', function ($table) {
                $table->string('facebook_link', 255)->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['telegram_username'])) {
            Schema::table('users', function ($table) {
                $table->string('telegram_username', 255)->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['btc_link'])) {
            Schema::table('users', function ($table) {
                $table->string('btc_link', 255)->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['btc_username'])) {
            Schema::table('users', function ($table) {
                $table->string('btc_username', 255)->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['youtube_link'])) {
            Schema::table('users', function ($table) {
                $table->string('youtube_link', 255)->nullable();
            });
        }


        if (!Schema::hasTable('cryptopolice_academy_exams')) {

            Schema::create('cryptopolice_academy_exams', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('exam_title', 255);
                $table->string('exam_description', 255);
                $table->string('exam_slug', 255);
                $table->text('question');
                $table->integer('timer');
                $table->integer('retake_time')->unsigned(false)->default(0);
                $table->boolean('status')->default(0);
                $table->integer('question_count')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_academy_final_exam_score')) {

            Schema::create('cryptopolice_academy_final_exam_score', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->integer('user_id')->default(0);
                $table->integer('exam_id')->default(0);
                $table->integer('score')->default(0);
                $table->dateTime('completed_at');
                $table->boolean('complete_status')->default(0);
                $table->integer('try')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_academy_scores')) {

            Schema::create('cryptopolice_academy_scores', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->integer('exam_id');
                $table->integer('question_num');
                $table->integer('answer_num');
                $table->boolean('is_correct');
                $table->integer('user_id');
                $table->integer('try')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }


        if (!Schema::hasTable('cryptopolice_academy_trainings')) {

            Schema::create('cryptopolice_academy_trainings', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->integer('category_id')->nullable();
                $table->text('description')->nullable();
                $table->integer('user_id')->default(0);
                $table->boolean('status')->default(0);
                $table->integer('likes')->default(0);
                $table->integer('sort_order')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_academy_training_views')) {

            Schema::create('cryptopolice_academy_training_views', function ($table) {
                $table->engine = 'InnoDB';
                $table->integer('user_id')->default(0);
                $table->integer('training_id')->default(0);
            });
        }

        if (!Schema::hasTable('cryptopolice_academy_trainings_category')) {

            Schema::create('cryptopolice_academy_trainings_category', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('description')->nullable();
                $table->integer('user_id')->default(0);
                $table->boolean('status');
                $table->integer('sort_order')->default(0);
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
