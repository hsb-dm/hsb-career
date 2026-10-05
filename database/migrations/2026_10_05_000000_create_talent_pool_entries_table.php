<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('talent_pool_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->index();
            $table->string('area_of_interest');
            $table->string('cv_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_pool_entries');
    }
};
