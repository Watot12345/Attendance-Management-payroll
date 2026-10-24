<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public / Guest Routes
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('landing');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Kiosk / QR Scanner (Can be accessed standalone for dedicated tablet/doorway terminal)
Route::get('/scanner', [AttendanceController::class, 'scanner'])->name('scanner');
Route::post('/scanner/verify', [AttendanceController::class, 'verifyQr'])->name('scanner.verify');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Attendance
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances/clock', [AttendanceController::class, 'selfClock'])->name('attendances.clock');

    // Leaves
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::delete('/leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

    // Payrolls
    Route::get('/payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('/payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('/payrolls/{payroll}/print', [PayrollController::class, 'print'])->name('payrolls.print');

    // Admin Only Management Routes
    Route::middleware('admin')->group(function () {
        // Attendance overrides
        Route::post('/attendances/manual', [AttendanceController::class, 'storeManual'])->name('attendances.manual');
        Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

        // Leave reviews
        Route::patch('/leaves/{leave}/status', [LeaveController::class, 'updateStatus'])->name('leaves.update-status');

        // Payroll processing
        Route::post('/payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
        Route::patch('/payrolls/{payroll}/status', [PayrollController::class, 'updateStatus'])->name('payrolls.update-status');

        // Employees Management
        Route::resource('employees', EmployeeController::class);
        Route::post('/employees/{employee}/regenerate-qr', [EmployeeController::class, 'regenerateQr'])->name('employees.regenerate-qr');

        // Company Settings
        Route::get('/settings', [CompanySettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [CompanySettingController::class, 'update'])->name('settings.update');
    });
});
