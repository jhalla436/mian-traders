<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('company_contacts')) {
            return;
        }

        Schema::create('company_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            $table->string('name', 120);
            $table->string('role_title', 120)->nullable();

            $table->string('phone_primary', 40)->nullable();
            $table->string('phone_alt', 40)->nullable();
            $table->string('email', 120)->nullable();

            $table->string('address', 255)->nullable();
            $table->text('note')->nullable();

            $table->unsignedBigInteger('reports_to_contact_id')->nullable();
            $table->foreign('reports_to_contact_id')
                ->references('id')
                ->on('company_contacts')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['company_id', 'is_active']);
            $table->index(['company_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_contacts');
    }
};
