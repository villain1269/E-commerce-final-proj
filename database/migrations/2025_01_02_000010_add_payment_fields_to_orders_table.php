<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 30)->default('pending_payment')->after('payment');
            $table->string('payment_intent_id', 60)->nullable()->after('payment_status');
            $table->string('payment_method_type', 30)->nullable()->after('payment_intent_id');
            $table->string('payment_ref', 60)->nullable()->after('payment_method_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'payment_intent_id', 'payment_method_type', 'payment_ref']);
        });
    }
};
