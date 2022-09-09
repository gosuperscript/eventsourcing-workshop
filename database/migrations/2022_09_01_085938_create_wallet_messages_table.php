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
        Schema::create('wallet_messages', function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->uuid('event_id');
            $table->uuid('aggregate_root_id');
            $table->integer('version');
            $table->text('payload');
            $table->index(['aggregate_root_id', 'version']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_messages');
    }
};
