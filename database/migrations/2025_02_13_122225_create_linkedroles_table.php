<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('linked_role_settings', function (Blueprint $table) {
            $table->id();
            $table->text('discordlinkedroles_client_id');
            $table->text('discordlinkedroles_client_secret');
            $table->text('discordlinkedroles_bot_token');
        });

        DB::table('linked_role_settings')->insert([
            [
                'discordlinkedroles_client_id' => '1296581432969265306',
                'discordlinkedroles_client_secret' => 'RANDOMEXAMPLESECRET1234567890',
                'discordlinkedroles_bot_token' => 'RANDOMEXAMPLETOKEN1234567890',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_role_settings');
    }
};
