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
        Schema::create('lost_item_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->string('item_name');
            $table->string('category');
            $table->text('description');
            $table->string('location');
            $table->date('date_lost');
            $table->string('photo_url')->nullable();
            $table->enum('status', ['active', 'resolved'])->default('active');
            $table->timestamps();

            $table ->fullText(['item_name', 'category','description', 'location'])->name('lost_reports_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_item_reports');
    }
};
