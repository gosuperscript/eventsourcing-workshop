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
        DB::statement("CREATE TABLE IF NOT EXISTS `wallet_messages` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `event_id` BINARY(16) NOT NULL,
            `aggregate_root_id` BINARY(16) NOT NULL,
            `version` int(20) unsigned NULL,
            `payload` varchar(16001) NOT NULL,
            PRIMARY KEY (`id` ASC),
            KEY `reconstitution` (`aggregate_root_id`, `version` ASC)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB;"
        );
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
