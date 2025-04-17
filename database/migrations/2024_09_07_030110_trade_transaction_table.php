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
        Schema::create('trade_transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamp('created_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('ended_at')->nullable();
            $table->enum('state', ['open','send','done','cancel','dispute']);
            $table->text('cancelReason')->nullable();
            $table->enum('order_type', ['buy', 'sell']);
            $table->unsignedBigInteger('offer_user_id')->default(0);
            $table->unsignedBigInteger('client_user_id')->default(0);
            $table->decimal('tetherAmount', 20, 6)->default(0);
            $table->decimal('fee', 20, 6)->default(0);
            $table->tinyInteger('priceType')->default(0)->comment('0:marketPrice, 1:fixedPrice');
            $table->decimal('price', 15, 2);
            $table->decimal('margin', 5, 2)->default(0);
            $table->decimal('totalPayAmount', 15, 2);
            $table->string('bankName')->nullable();
            $table->string('accountNumber')->nullable();
            $table->string('accountName')->nullable();
            $table->string('QRImage')->nullable();
            $table->string('payProof')->nullable();
            $table->bigInteger('buyListId')->default(0);
            $table->bigInteger('sellListId')->default(0);
            $table->boolean('isTransferred')->default(false);

            $table->index('created_at');
            $table->index('ended_at');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trade_transactions');
    }
};
