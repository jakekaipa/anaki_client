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
        Schema::create('forexcrow_offers', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('forexcrow_id')->constrained('forexcrows')->cascadeOnDelete();
            $table->foreignId('for_user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->nullable()->cconstrained('users')->cascadeOnDelete();
            $table->decimal('amount', 28, 16);
            $table->decimal('rate', 28, 16);
            $table->foreignId('rate_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->foreignId('sale_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->enum('status', [1,2,3,4])->comment('1: Accepet, 2: Pending, 3: Sold, 4: Rejected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forexcrow_offers');
    }
};
