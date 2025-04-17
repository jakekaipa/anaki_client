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
        Schema::create('forexcrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->decimal('amount', 28, 16);
            $table->decimal('rate', 28, 16);
            $table->foreignId('rate_currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->text('comment')->nullable();
            $table->enum('status', [1,2,3,4,5,6,7,8])->default(2)->comment('1=Ongoing,2=Pending,3=Accepted,4=Rejected,5=Payment Pending,6=Complete,7=Cancel Request,8=Cancel Buy User');
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
        Schema::dropIfExists('forexcrows');
    }
};
