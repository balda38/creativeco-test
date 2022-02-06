<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountBuyTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account_buy_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_account_id');
            $table->unsignedSmallInteger('currency_id');
            $table->double('value');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('buy_before')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreign('user_account_id')
                ->references('id')->on('user_accounts')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('currency_id')
                ->references('id')->on('currencies')
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
        Schema::dropIfExists('user_account_buy_tasks');
    }
}
