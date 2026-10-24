<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'basic_salary',
        'days_worked',
        'regular_hours',
        'hourly_rate',
        'attendance_pay',
        'overtime_hours',
        'overtime_pay',
        'late_minutes',
        'late_deduction',
        'sss_deduction',
        'insurance_deduction',
        'tax_deduction',
        'other_deductions',
        'gross_pay',
        'total_deductions',
        'net_pay',
        'status',
        'payment_method',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'payment_date' => 'date',
            'basic_salary' => 'decimal:2',
            'regular_hours' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'attendance_pay' => 'decimal:2',
            'overtime_hours' => 'decimal:2',
            'overtime_pay' => 'decimal:2',
            'late_deduction' => 'decimal:2',
            'sss_deduction' => 'decimal:2',
            'insurance_deduction' => 'decimal:2',
            'tax_deduction' => 'decimal:2',
            'other_deductions' => 'decimal:2',
            'gross_pay' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_pay' => 'decimal:2',
            'days_worked' => 'integer',
            'late_minutes' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
