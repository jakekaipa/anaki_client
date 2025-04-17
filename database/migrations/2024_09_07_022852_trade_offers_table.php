<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_offers', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('user_id');
            $table->enum('order_type', ['buy', 'sell']);
            $table->string('token_type')->default('USDT');
            $table->text('bankName')->nullable();
            $table->text('accountNumber')->nullable();
            $table->text('accountName')->nullable();
            $table->text('payQR')->nullable();
            $table->tinyInteger('priceType')->nullable();
            $table->decimal('tradeVolMin', 20, 8)->unsigned()->nullable();
            $table->decimal('tradeVolMax', 20, 8)->unsigned()->nullable();
            $table->unsignedInteger('offerMargin')->nullable();
            $table->decimal('fixedPrice', 15, 2)->nullable();
            $table->unsignedInteger('offerTimeLimit')->nullable();
            $table->text('offerTag')->nullable();
            $table->text('offerLabel')->nullable();
            $table->text('offerCondition')->nullable();
            $table->text('transGuide')->nullable();
            $table->boolean('needMobileAuth')->nullable();
            $table->boolean('needKYCAuth')->nullable();
            $table->boolean('needAccountAuth')->nullable();
            $table->text('password')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_offers');
    }
};
