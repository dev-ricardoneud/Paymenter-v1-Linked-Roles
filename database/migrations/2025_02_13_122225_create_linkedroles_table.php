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
            $table->text('discordlinkedroles_bot_name');
            $table->text('discordlinkedroles_client_id');
            $table->text('discordlinkedroles_client_secret');
            $table->text('discordlinkedroles_bot_token');
            $table->text('syncedwithpaymenter');
            $table->text('syncedwithpaymenter_description');
            $table->text('activeproducts');
            $table->text('activeproducts_description');
            $table->text('api_url');
            $table->text('api_url_version');
            $table->text('linkedroles_url');
            $table->text('linkedroles_callback_url');
            $table->text('callback_redirect_page');
            $table->text('success_route')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_role_settings');
    }
};
