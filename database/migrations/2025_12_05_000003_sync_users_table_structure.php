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
        Schema::table('users', function (Blueprint $table) {
            // Add role column if not exists
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->nullable()->after('email');
            }

            // Add approval_status column if not exists
            if (! Schema::hasColumn('users', 'approval_status')) {
                $table->string('approval_status')->default('pending')->after('is_approved');
            }

            // Add approved_at column if not exists
            if (! Schema::hasColumn('users', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approval_status');
            }

            // Add rejection_reason column if not exists
            if (! Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'approval_status')) {
                $table->dropColumn('approval_status');
            }

            if (Schema::hasColumn('users', 'approved_at')) {
                $table->dropColumn('approved_at');
            }

            if (Schema::hasColumn('users', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
