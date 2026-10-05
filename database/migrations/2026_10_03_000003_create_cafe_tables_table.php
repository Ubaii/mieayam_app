<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cafe_tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number')->unique();
            $table->unsignedSmallInteger('capacity');
            $table->string('location')->nullable();
            $table->string('status')->default('available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cafe_tables');
    }
};
