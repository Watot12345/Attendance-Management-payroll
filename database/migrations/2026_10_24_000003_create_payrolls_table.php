<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('basic_salary', 10, 2);
            $table->integer('days_worked')->default(0);
            $table->decimal('regular_hours', 6, 2)->default(0.00);
            $table->decimal('hourly_rate', 10, 2)->default(0.00);
            $table->decimal('attendance_pay', 10, 2)->default(0.00);
            $table->decimal('overtime_hours', 6, 2)->default(0.00);
            $table->decimal('overtime_pay', 10, 2)->default(0.00);
            $table->integer('late_minutes')->default(0);
            $table->decimal('late_deduction', 10, 2)->default(0.00);
            $table->decimal('sss_deduction', 10, 2)->default(300.00);
            $table->decimal('insurance_deduction', 10, 2)->default(300.00);
            $table->decimal('tax_deduction', 10, 2)->default(0.00);
            $table->decimal('other_deductions', 10, 2)->default(0.00);
            $table->decimal('gross_pay', 10, 2)->default(0.00);
            $table->decimal('total_deductions', 10, 2)->default(0.00);
            $table->decimal('net_pay', 10, 2)->default(0.00);
            $table->enum('status', ['draft', 'processed', 'paid'])->default('processed');
            $table->string('payment_method')->default('Bank Transfer');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
