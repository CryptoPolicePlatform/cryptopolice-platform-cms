<?php namespace CryptoPolice\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CryptoPolicePlatformAcademyTables extends Migration
{

    /*
     * CryptoPolice Academy Tables
     */

    public function up()
    {

        /*
         * Academy tables
         */

        if (!Schema::hasTable('cryptopolice_cryptopolice_exams')) {

            Schema::create('cryptopolice_cryptopolice_exams', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('exam_title', 255);
                $table->string('exam_description', 255);
                $table->string('exam_slug', 255);
                $table->text('question');
                $table->integer('timer');
                $table->integer('retake_time')->unsigned(false)->default(0);
                $table->boolean('status')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_cryptopolice_final_exam_score')) {

            Schema::create('cryptopolice_cryptopolice_final_exam_score', function ($table) {
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

        if (!Schema::hasTable('cryptopolice_cryptopolice_scores')) {

            Schema::create('cryptopolice_cryptopolice_scores', function ($table) {
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

        if (!Schema::hasTable('cryptopolice_cryptopolice_trainings')) {

            Schema::create('cryptopolice_cryptopolice_trainings', function ($table) {
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

        if (!Schema::hasTable('cryptopolice_cryptopolice_trainings_category')) {

            Schema::create('cryptopolice_cryptopolice_trainings_category', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('description')->nullable();
                $table->integer('user_id')->default(0);
                $table->integer('sort_order')->default(0);
                $table->boolean('status');
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasColumns('users', ['eth_address'])) {

            Schema::table('users', function ($table) {
                $table->string('eth_address', 42)->nullable()->unique();
            });
        }

        /*
         * Bounty tables
         */

        if (!Schema::hasTable('cryptopolice_cryptopolice_bounty_campaigns')) {

            Schema::create('cryptopolice_cryptopolice_bounty_campaigns', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('description')->nullable();
                $table->boolean('status')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_cryptopolice_bounty_users')) {

            Schema::create('cryptopolice_cryptopolice_bounty_users', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->integer('user_id')->default(0);
                $table->integer('rewards_id')->default(0);
                $table->integer('bounty_campaigns_id')->default(0);
                $table->integer('given_reward')->default(0);
                $table->boolean('status')->default(0);
                $table->text('description')->nullable();
                $table->string('title', 255)->nullable();
                $table->string('comment', 255)->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();

            });
        }

        if (!Schema::hasTable('cryptopolice_cryptopolice_rewards')) {

            Schema::create('cryptopolice_cryptopolice_rewards', function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->integer('bounty_campaigns_id')->default(0);
                $table->string('reward_title', 255)->nullable();
                $table->string('reward_description', 255)->nullable();
                $table->boolean('reward_type', 255)->default(0);
                $table->integer('reward_amount')->default(0);
                $table->boolean('status')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }
    }

    public function down()
    {

        /*
         * Academy tables
         */

        Schema::dropIfExists('cryptopolice_cryptopolice_trainings_category');
        Schema::dropIfExists('cryptopolice_cryptopolice_final_exam_score');
        Schema::dropIfExists('cryptopolice_cryptopolice_trainings');
        Schema::dropIfExists('cryptopolice_cryptopolice_scores');
        Schema::dropIfExists('cryptopolice_cryptopolice_exams');

        /*
         * Bounty tables
         */

        Schema::dropIfExists('cryptopolice_cryptopolice_bounty_campaigns');
        Schema::dropIfExists('cryptopolice_cryptopolice_bounty_users');
        Schema::dropIfExists('cryptopolice_cryptopolice_rewards');

        /*
         * Bounty tables
         */

        if (Schema::hasTable('users')) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['eth_address']);
            });
        }
    }
}
