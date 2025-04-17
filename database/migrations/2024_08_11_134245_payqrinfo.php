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
        Schema::create('payqrinfo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userid')->default(0);
            $table->tinyInteger('index')->nullable();
            $table->string('payTag', 255)->nullable();
            $table->text('payQR')->nullable();

            $table->index('userid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payqrinfo');
    }
};
