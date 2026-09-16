<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Purely additive and entirely nullable, so existing rows stay valid and
     * the running application keeps working before the new code is deployed.
     *
     * email_status is a plain string rather than a DB enum: adding a state
     * later then costs no migration, and App\Enums\EmailStatus keeps the
     * values honest at the PHP boundary.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('email_subject')->nullable();

            $table->longText('email_body')->nullable();

            $table->string('email_status', 20)->default('pending')->index();

            $table->timestamp('email_generated_at')->nullable();

            $table->timestamp('email_approved_at')->nullable();

            $table->timestamp('email_sent_at')->nullable();

            $table->text('email_error')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'email_subject',
                'email_body',
                'email_status',
                'email_generated_at',
                'email_approved_at',
                'email_sent_at',
                'email_error',
            ]);
        });
    }
};
