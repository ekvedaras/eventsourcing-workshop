<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('wallet_balance', function (Blueprint $table) {
            $table->uuid('wallet_id')->primary();
            $table->bigInteger('tokens');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_balance');
    }
};