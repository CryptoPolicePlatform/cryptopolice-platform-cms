<?php namespace CryptoPolice\Bounty\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CryptoPolicebountyBountyTables extends Migration
{

    /*
     * CryptoPolice Bounty Tables
     */

    public function up()
    {

        if (!Schema::hasTable('cryptopolice_bounty_bounty_campaigns')) {

            Schema::create('cryptopolice_bounty_bounty_campaigns', function ($table) {

                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('description')->nullable();
                $table->boolean('status')->default(0);
                $table->integer('sort_order')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_bounty_bounty_users')) {

            Schema::create('cryptopolice_bounty_bounty_users', function ($table) {
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

        if (!Schema::hasTable('cryptopolice_bounty_rewards')) {

            Schema::create('cryptopolice_bounty_rewards', function ($table) {
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

         Schema::dropIfExists('cryptopolice_bounty_bounty_campaigns');
         Schema::dropIfExists('cryptopolice_bounty_bounty_users');
         Schema::dropIfExists('cryptopolice_bounty_rewards');

    }
}
