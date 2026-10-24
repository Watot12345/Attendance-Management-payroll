<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'late_minutes',
        'overtime_minutes',
        'work_hours',
        'scan_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'work_hours' => 'decimal:2',
            'late_minutes' => 'integer',
            'overtime_minutes' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
