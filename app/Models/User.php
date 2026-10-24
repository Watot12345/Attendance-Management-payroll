<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'qr_secret',
        'phone',
        'department',
        'position',
        'basic_salary',
        'daily_rate',
        'hourly_rate',
        'status',
        'hire_date',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'basic_salary' => 'decimal:2',
            'daily_rate' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'hire_date' => 'date',
        ];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->qr_secret)) {
                $user->qr_secret = Str::random(32);
            }
            if (empty($user->daily_rate) && !empty($user->basic_salary)) {
                $user->daily_rate = round($user->basic_salary / 22, 2);
            }
            if (empty($user->hourly_rate) && !empty($user->daily_rate)) {
                $user->hourly_rate = round($user->daily_rate / 8, 2);
            }
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(public_path('images/' . $this->avatar))) {
            return asset('images/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=4f46e5&color=fff&size=128';
    }
}
