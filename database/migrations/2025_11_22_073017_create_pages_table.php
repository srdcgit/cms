<?php

// database/migrations/2025_01_01_000000_create_pages_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('html')->nullable();
            $table->longText('css')->nullable();
            $table->longText('js')->nullable(); 
            $table->longText('gjs_json')->nullable(); // GrapesJS data
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('pages');
    }
};
