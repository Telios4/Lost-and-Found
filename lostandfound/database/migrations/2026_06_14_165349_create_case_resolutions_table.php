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
        Schema::create('case_resolutions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lost_report_id')->constrained('lost_item_reports')->onDelete('cascade');
            $table->foreignUuid('found_report_id')->constrained('found_item_reports')->onDelete('cascade');
            $table->foreignUuid('resolved_by_user')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('resolved_by_admin')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamp('resolved_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_resolutions');
    }
};
