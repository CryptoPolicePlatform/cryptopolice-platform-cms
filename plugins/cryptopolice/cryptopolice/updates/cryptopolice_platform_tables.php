<?php namespace CryptoPolice\CryptoPolice\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CryptopolicPlatformTables extends Migration {

    public function up()
    {
        // Drop previous version tables TODO: remove it after next update
        Schema::dropIfExists('academy_cryptopolice_exams');
        Schema::dropIfExists('academy_cryptopolice_final_exam_score');
        Schema::dropIfExists('academy_cryptopolice_scores');
        Schema::dropIfExists('academy_cryptopolice_trainings');

        Schema::create('cryptopolice_cryptopolice_exams', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('exam_title', 255);
            $table->string('exam_description', 255);
            $table->string('exam_slug', 255);
            $table->text('question');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('timer');
            $table->boolean('status')->default(0);
            $table->integer('retake_time')->unsigned(false)->default(0);
        });

        Schema::create('cryptopolice_cryptopolice_final_exam_score', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->default(0);
            $table->integer('exam_id')->default(0);
            $table->integer('score')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->dateTime('completed_at');
            $table->boolean('complete_status')->default(0);
            $table->integer('question_num');
            $table->integer('answer_num');
            $table->boolean('is_correct');
            $table->integer('try')->default(0);
        });


        Schema::create('cryptopolice_cryptopolice_trainings', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->integer('category_id')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('user_id')->default(0);
            $table->boolean('status')->default(0);
            $table->integer('likes')->default(0);
        });

        Schema::create('cryptopolice_cryptopolice_trainings_category', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('title')->nullable();
            $table->text('slug')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->integer('user_id')->default(0);
            $table->integer('nest_left')->default(0);
            $table->boolean('status');
        });

        if (Schema::hasColumns('users', ['eth_address'])) {
            return;
        }

        Schema::table('users', function ($table) {
            $table->string('eth_address', 42)->nullable()->unique();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cryptopolice_cryptopolice_exams');
        Schema::dropIfExists('cryptopolice_cryptopolice_final_exam_score');
        Schema::dropIfExists('cryptopolice_cryptopolice_scores');
        Schema::dropIfExists('cryptopolice_cryptopolice_trainings');
        Schema::dropIfExists('cryptopolice_cryptopolice_trainings_category');

        if (Schema::hasTable('users')) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['eth_address']);
            });
        }
    }
}
