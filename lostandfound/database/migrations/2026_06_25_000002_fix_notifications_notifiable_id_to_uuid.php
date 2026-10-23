<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex(['notifiable_type', 'notifiable_id']);
            });

            DB::statement('ALTER TABLE notifications MODIFY notifiable_id CHAR(36) NOT NULL');

            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_type', 'notifiable_id']);
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex(['notifiable_type', 'notifiable_id']);
            });

            DB::statement('ALTER TABLE notifications MODIFY notifiable_id BIGINT UNSIGNED NOT NULL');

            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_type', 'notifiable_id']);
            });
        }
    }
};
