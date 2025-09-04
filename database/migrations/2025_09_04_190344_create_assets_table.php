<?php

use App\Enums\AssetTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')
                ->unique();
            $table->string('symbol')
                ->unique();
            $table->string('type')
                ->default(AssetTypeEnum::TOKEN);
            $table->integer('decimals');
            $table->string('logo');
            $table->text('description');
            $table->string('website')
                ->nullable();
            $table->string('twitter')
                ->nullable();
            $table->string('discord')
                ->nullable();
            $table->string('telegram')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
