<?php

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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reminder_category_id');
            $table->unsignedBigInteger('priority_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('repeat')->default(false);
            $table->enum('type', ['daily', 'weekly', 'monthly'])->nullable();
            $table->integer('interval')->nullable();
            $table->date('due_date')->nullable();
            $table->date('end_date')->nullable(); 
            $table->string('weekdays')->nullable();
            $table->string('monthdays')->nullable(); 
            $table->foreign('reminder_category_id')->references('id')->on('reminder_categories')->onDelete('cascade');
            $table->foreign('priority_id')->references('id')->on('priorities')->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
