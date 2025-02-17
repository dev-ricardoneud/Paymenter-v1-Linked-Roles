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
        Schema::create('linked_role_settings_custom_pages', function (Blueprint $table) {
            $table->id();
            $table->text('key');
            $table->text('name');
            $table->text('title');
            $table->text('text');
            $table->text('text_above_button');
            $table->text('button_text');
            $table->text('button_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_role_settings_custom_pages');
    }
};
