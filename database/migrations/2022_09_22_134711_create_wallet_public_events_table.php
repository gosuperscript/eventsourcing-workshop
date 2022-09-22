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
        Schema::create('wallet_public_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('message_id');
            $table->string('topic');
            $table->string('message_type');
            $table->jsonb('payload');
            $table->jsonb('headers');
            $table->timestamp('published_at');

            $table->index(['topic', 'id'], 'topic');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallet_public_events');
    }
};
