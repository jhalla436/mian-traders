<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_ledger_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->date('entry_date');

            // purchase|payment|transport|adjustment|credit|debit etc
            $table->string('entry_type', 40)->default('adjustment');

            // debit increases payable (we owe company more), credit decreases payable
            $table->enum('direction', ['debit', 'credit'])->default('debit');
            $table->decimal('amount', 14, 2)->default(0);

            $table->string('description', 255)->nullable();

            // Optional reference to link to a purchase / payment record
            $table->string('ref_type', 40)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['company_id', 'entry_date']);
            $table->index(['company_id', 'entry_type']);
            $table->index(['ref_type', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_ledger_entries');
    }
};
