<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('cafe_table_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_method');
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('total');
            $table->unsignedInteger('amount_paid');
            $table->unsignedInteger('change_amount')->default(0);
            $table->text('note')->nullable();
            $table->string('status')->default('completed');
            $table->timestamp('paid_at');
            $table->timestamps();
            $table->index(['paid_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
