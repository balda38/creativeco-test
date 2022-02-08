<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnCurrencyIdToGoalUserAccountIdInTableUserAccountBuyTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_account_buy_tasks', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id']);

            $table->unsignedBigInteger('goal_user_account_id')->after('user_account_id');
            $table->foreign('goal_user_account_id')
                ->references('id')->on('user_accounts')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Don't support migration down
    }
}
