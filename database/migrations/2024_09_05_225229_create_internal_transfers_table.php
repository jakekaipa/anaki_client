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
        Schema::create('internal_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('sender_email');
            $table->string('receiver_email');
            $table->decimal('amount', 15, 8);  // 높은 정밀도를 위해 15자리, 소수점 이하 8자리 사용
            $table->decimal('fee', 15, 8);
            $table->timestamps();

            // 인덱스 추가
            $table->index('sender_email');
            $table->index('receiver_email');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal_transfers');
    }
};
