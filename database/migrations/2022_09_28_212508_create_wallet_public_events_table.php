<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('wallet_public_events', function (Blueprint $table) {
            $table->id();
            $table->string('message_id');
            $table->string('topic');
            $table->string('message_type');
            $table->longText('payload');
            $table->longText('headers');
            $table->timestamp('published_at');
            $table->index(['topic', 'id'], 'topic');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_public_events');
    }
};