<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Добавляем поля для платежей
            $table->unsignedBigInteger('payment_id')->nullable()->after('course_id');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_id');
            $table->string('currency', 10)->default('USD')->after('amount_paid');
            $table->string('payment_method', 50)->nullable()->after('currency');
            $table->string('payment_status', 20)->default('pending')->after('payment_method');
            $table->string('transaction_id')->nullable()->after('payment_status');
            $table->timestamp('payment_date')->nullable()->after('transaction_id');
            $table->json('payment_data')->nullable()->after('payment_date');

            // Внешний ключ для связи с payments таблицей
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn([
                'payment_id',
                'amount_paid',
                'currency',
                'payment_method',
                'payment_status',
                'transaction_id',
                'payment_date',
                'payment_data'
            ]);
        });
    }
};
