<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('from_currency_id');
            $table->unsignedSmallInteger('to_currency_id');
            $table->double('value');
            $table->timestamps();

            $table->foreign('from_currency_id')
                ->references('id')->on('currencies')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('to_currency_id')
                ->references('id')->on('currencies')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['from_currency_id', 'to_currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currency_exchange_rates');
    }
}
