<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('event_id')->primary();
            $table->string('wallet_id')->index();
            $table->integer('amount');
            $table->dateTime('transacted_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
