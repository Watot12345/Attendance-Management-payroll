@extends('layouts.app')

@section('title', 'Register New Employee')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-2 text-xs font-mono">
        <a href="{{ route('employees.index') }}" class="text-green-400 hover:underline font-bold">&larr; Back to Employees</a>
    </div>

    <div class="sigla-card p-8">
        <div class="pb-4 border-b border-green-800">
            <h1 class="text-2xl font-black text-white tracking-wider uppercase">
                <i class="fas fa-user-plus text-green-400 mr-2"></i> Register New Staff Member
            </h1>
            <p class="text-xs text-slate-400 font-mono mt-1">This will automatically generate a unique biometric QR token for attendance scanning.</p>
        </div>

        <form action="{{ route('employees.store') }}" method="POST" class="space-y-4 pt-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Juan Gomez" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Username</label>
                    <input type="text" name="username" required value="{{ old('username') }}" placeholder="e.g. jgomez" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="e.g. jgomez@siglatrolap.com" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0912 345 6789" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Department</label>
                    <input type="text" name="department" required value="{{ old('department', 'Engineering') }}" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Job Title</label>
                    <input type="text" name="position" required value="{{ old('position', 'Software Engineer') }}" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Monthly Basic Salary (PHP)</label>
                    <input type="number" step="0.01" name="basic_salary" required value="{{ old('basic_salary', '50000.00') }}" class="sigla-input w-full text-xs">
                    <p class="text-[10px] text-green-400 font-mono mt-0.5">Daily & hourly rates will be auto-calculated.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Hire Date</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Account Password</label>
                <input type="password" name="password" required value="password" class="sigla-input w-full text-xs">
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-green-800">
                <a href="{{ route('employees.index') }}" class="px-4 py-2 text-xs font-mono text-slate-400 hover:text-white">
                    Cancel
                </a>
                <button type="submit" class="btn-sigla-green text-xs !py-2.5 !px-6">
                    <i class="fas fa-qrcode mr-2"></i> Register & Generate QR Pass
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
