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
        Schema::create('bankinfo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userid')->default(0);
            $table->tinyInteger('index')->nullable();
            $table->string('accountTag', 255)->nullable();
            $table->string('bankName', 255)->nullable();
            $table->string('accountNumber', 255)->nullable();
            $table->string('accountHolder', 255)->nullable();

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
        Schema::dropIfExists('bankinfo');
    }
};
