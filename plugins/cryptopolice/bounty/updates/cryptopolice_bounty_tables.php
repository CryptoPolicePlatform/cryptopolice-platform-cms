<?php namespace CryptoPolice\Bounty\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use cryptopolice\airdrop\Models\AirdropRegistration;

class CryptoPolicebountyBountyTables extends Migration
{

    public function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function ($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }


    /*
     * CryptoPolice Bounty Tables
     */

    public function up()
    {

        Schema::table('cryptopolice_bounty_user_reports', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_bounty_user_reports');

            if (!in_array('bounty_report_user_id_foreign', $foreignKeys)) {
                $table->integer('user_id')->unsigned()->nullable()->change();
                $table->foreign('user_id', 'bounty_report_user_id_foreign')->references('id')->on('users');
            }

            if (!in_array('bounty_report_reward_id_foreign', $foreignKeys)) {
                $table->integer('reward_id')->unsigned()->nullable()->change();
                $table->foreign('reward_id', 'bounty_report_reward_id_foreign')->references('id')->on('cryptopolice_bounty_rewards');
            }

            if (!in_array('bounty_report_bounty_id_foreign', $foreignKeys)) {
                $table->integer('bounty_campaigns_id')->unsigned()->default(null)->change();
                $table->foreign('bounty_campaigns_id', 'bounty_report_bounty_id_foreign')->references('id')->on('cryptopolice_bounty_campaigns');
            }

            if (!in_array('bounty_report_registration_id_foreign', $foreignKeys)) {
                $table->integer('bounty_user_registration_id')->unsigned()->default(null)->change();
                $table->foreign('bounty_user_registration_id', 'bounty_report_registration_id_foreign')->references('id')->on('cryptopolice_bounty_user_registration');
            }

        });

        Schema::table('cryptopolice_bounty_user_registration', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_bounty_user_registration');

            if (!in_array('bounty_registration_user_id_foreign', $foreignKeys)) {
                $table->integer('user_id')->unsigned()->nullable()->change();
                $table->foreign('user_id', 'bounty_registration_user_id_foreign')->references('id')->on('users');
            }

            if (!in_array('bounty_registration_bounty_id_foreign', $foreignKeys)) {
                $table->integer('bounty_campaigns_id')->unsigned()->nullable()->change();
                $table->foreign('bounty_campaigns_id', 'bounty_registration_bounty_id_foreign')->references('id')->on('cryptopolice_bounty_campaigns');
            }
        });

        Schema::table('cryptopolice_airdrop_user_registration', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_airdrop_user_registration');

            if (!in_array('airdrop_registration_user_id_foreign', $foreignKeys)) {
                $table->integer('user_id')->unsigned()->default(1)->change();
                $table->foreign('user_id', 'airdrop_registration_user_id_foreign')->references('id')->on('users');
            }

            AirdropRegistration::withTrashed()->where('airdrop_id', 0)->update(['airdrop_id' => 1]);

            if (!in_array('airdrop_registration_airdrop_id_foreign', $foreignKeys)) {
                $table->integer('airdrop_id')->unsigned()->default(1)->change();
                $table->foreign('airdrop_id', 'airdrop_registration_airdrop_id_foreign')->references('id')->on('cryptopolice_airdrop_airdrop');
            }

        });

        Schema::table('cryptopolice_platform_community_posts', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_platform_community_posts');

            if (!in_array('post_user_id_foreign', $foreignKeys)) {
                $table->integer('user_id')->unsigned()->nullable()->change();
                $table->foreign('user_id', 'post_user_id_foreign')->references('id')->on('users');
            }

        });

        Schema::table('cryptopolice_platform_community_post_views', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_platform_community_post_views');

            if (!in_array('post_view_post_id_foreign', $foreignKeys)) {
                $table->integer('post_id')->unsigned()->nullable()->change();
                $table->foreign('post_id', 'post_view_post_id_foreign')->references('id')->on('cryptopolice_platform_community_posts');
            }

        });

        Schema::table('cryptopolice_platform_community_comment', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_platform_community_comment');

            if (!in_array('comment_user_id_foreign', $foreignKeys)) {
                $table->integer('user_id')->unsigned()->nullable()->change();
                $table->foreign('user_id', 'comment_user_id_foreign')->references('id')->on('users');
            }

            if (!in_array('comment_post_id_foreign', $foreignKeys)) {
                $table->integer('post_id')->unsigned()->nullable()->change();
                $table->foreign('post_id', 'comment_post_id_foreign')->references('id')->on('cryptopolice_platform_community_posts');
            }

        });

        Schema::table('cryptopolice_academy_final_exam_score', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_academy_final_exam_score');

            if (!in_array('final_score_user_id_foreign', $foreignKeys)) {
                $table->integer('user_id')->unsigned()->nullable()->change();
                $table->foreign('user_id', 'final_score_user_id_foreign')->references('id')->on('users');
            }

            if (!in_array('final_score_exam_id_foreign', $foreignKeys)) {
                $table->integer('exam_id')->unsigned()->nullable()->change();
                $table->foreign('exam_id', 'final_score_exam_id_foreign')->references('id')->on('cryptopolice_academy_exams');
            }

        });

        Schema::table('cryptopolice_academy_scores', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_academy_scores');

            if (!in_array('score_user_id_foreign', $foreignKeys)) {
                $table->integer('user_id')->unsigned()->nullable()->change();
                $table->foreign('user_id', 'score_user_id_foreign')->references('id')->on('users');
            }

            if (!in_array('score_exam_id_foreign', $foreignKeys)) {
                $table->integer('exam_id')->unsigned()->nullable()->change();
                $table->foreign('exam_id', 'score_exam_id_foreign')->references('id')->on('cryptopolice_academy_exams');
            }

        });

        Schema::table('cryptopolice_academy_trainings', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_academy_trainings');

            if (!in_array('training_category_id_foreign', $foreignKeys)) {
                $table->integer('category_id')->unsigned()->nullable()->change();
                $table->foreign('category_id', 'training_category_id_foreign')->references('id')->on('cryptopolice_academy_trainings_category');
            }
        });

        Schema::table('cryptopolice_academy_training_views', function ($table) {

            $foreignKeys = $this->listTableForeignKeys('cryptopolice_academy_training_views');

            if (!in_array('training_view_user_id_foreign', $foreignKeys)) {
                $table->integer('user_id')->unsigned()->nullable()->change();
                $table->foreign('user_id', 'training_view_user_id_foreign')->references('id')->on('users');
            }

            if (!in_array('training_view_training_id_foreign', $foreignKeys)) {
                $table->integer('training_id')->unsigned()->nullable()->change();
                $table->foreign('training_id', 'training_view_training_id_foreign')->references('id')->on('cryptopolice_academy_trainings');
            }

        });



        Schema::table('cryptopolice_bounty_campaigns', function ($table) {
            $table->float('percentage')->nullable()->change();
        });

        Schema::table('cryptopolice_bounty_user_registration', function ($table) {
            $table->string('btc_code', 38)->nullable()->change();
        });

        if (!Schema::hasTable('cryptopolice_bounty_user_registration')) {

            Schema::create('cryptopolice_bounty_user_registration', function ($table) {

                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->integer('bounty_campaigns_id')->default(0);
                $table->integer('user_id')->default(0);
                $table->boolean('status')->default(1);
                $table->boolean('approval_type')->default(0);
                $table->text('fields_data')->nullable();
                $table->string('btc_code', 15)->default(0);
                $table->string('btc_username', 255)->nullable();
                $table->boolean('btc_status')->default(0);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();

            });
        }

        if (!Schema::hasColumns('cryptopolice_bounty_user_registration', ['message'])) {
            Schema::table('cryptopolice_bounty_user_registration', function ($table) {
                $table->string('message', 1000)->nullable();
            });
        }

        if (!Schema::hasColumns('cryptopolice_bounty_user_registration', ['reverified'])) {
            Schema::table('cryptopolice_bounty_user_registration', function ($table) {
                $table->boolean('reverified')->default(0);
            });
        }

        if (!Schema::hasColumns('cryptopolice_bounty_user_reports', ['report_list'])) {
            Schema::table('cryptopolice_bounty_user_reports', function ($table) {
                $table->string('report_list', 2500)->nullable();
            });
        }

        if (!Schema::hasTable('cryptopolice_bounty_user_reports')) {

            Schema::create('cryptopolice_bounty_user_reports', function ($table) {

                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->integer('user_id')->default(0);
                $table->integer('reward_id')->default(1);
                $table->integer('bounty_campaigns_id')->default(0);
                $table->integer('bounty_user_registration_id')->default(0);
                $table->integer('given_reward')->default(0);
                $table->boolean('report_status')->default(0);
                $table->text('description')->nullable();
                $table->string('title', 255)->nullable();
                $table->string('comment', 1000)->nullable();
                $table->text('fields_data')->nullable();
                $table->text('report_files')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->timestamp('deleted_at')->nullable();

            });
        }

        if (!Schema::hasTable('cryptopolice_bounty_campaigns')) {

            Schema::create('cryptopolice_bounty_campaigns', function ($table) {

                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('title', 255)->nullable();
                $table->string('slug', 255)->nullable();
                $table->text('description')->nullable();
                $table->boolean('status')->default(0);
                $table->integer('sort_order')->default(0);
                $table->text('fields')->nullable();
                $table->string('icon', 25)->nullable();
                $table->tinyInteger('percentage')->default(0);
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
                $table->boolean('reward_type')->default(0);
                $table->integer('reward_amount_min')->default(0);
                $table->integer('reward_amount_max')->default(0);
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
