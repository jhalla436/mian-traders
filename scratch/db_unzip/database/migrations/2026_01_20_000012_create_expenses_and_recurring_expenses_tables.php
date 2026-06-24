<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            $table->date('expense_date');
            $table->string('title', 160);
            $table->string('category', 80)->nullable();
            $table->string('vendor', 160)->nullable();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_method', 40)->nullable();
            $table->string('note', 255)->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['expense_date']);
            $table->index(['category']);
        });

        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();

            $table->string('title', 160);
            $table->string('category', 80)->nullable();
            $table->string('vendor', 160)->nullable();
            $table->decimal('amount', 14, 2)->default(0);

            // 1..28 recommended (safe for all months)
            $table->unsignedTinyInteger('day_of_month')->default(1);

            $table->boolean('is_active')->default(true);

            // YYYY-MM, used to prevent duplicate generation per month
            $table->string('last_generated_month', 7)->nullable();

            $table->string('note', 255)->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'day_of_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_expenses');
        Schema::dropIfExists('expenses');
    }
};