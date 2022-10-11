<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('process_managers', function (Blueprint $table) {
            $table->string('process_id')->primary();
            $table->string('type');
            $table->longText('payload');
        });
    }

    public function down()
    {
        Schema::dropIfExists('process_managers');
    }
};
