<?php namespace RainLab\UserPlus\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UserAddLocationFields extends Migration
{
    public function up()
    {
        if (Schema::hasColumns('users', ['eth_address'])) {
            return;
        }

        Schema::table('users', function($table)
        {
            $table->string('eth_address', 42)->nullable()->unique();
        });
    }

    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function ($table) {
                $table->dropColumn(['eth_address']);
            });
        }
    }
}
