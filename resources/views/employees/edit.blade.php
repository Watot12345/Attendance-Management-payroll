@extends('layouts.app')

@section('title', 'Edit Employee - ' . $employee->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center space-x-2 text-xs font-mono">
        <a href="{{ route('employees.show', $employee) }}" class="text-green-400 hover:underline font-bold">&larr; Back to Profile</a>
    </div>

    <div class="sigla-card p-8">
        <div class="pb-4 border-b border-green-800 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-white tracking-wider uppercase">
                    <i class="fas fa-user-edit text-green-400 mr-2"></i> Edit Employee Information
                </h1>
                <p class="text-xs text-slate-400 font-mono mt-1">Configure compensation and roster status.</p>
            </div>
            <img src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-green-500">
        </div>

        <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-4 pt-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name</label>
                    <input type="text" name="name" required value="{{ old('name', $employee->name) }}" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Username</label>
                    <input type="text" name="username" required value="{{ old('username', $employee->username) }}" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                    <input type="email" name="email" required value="{{ old('email', $employee->email) }}" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Department</label>
                    <input type="text" name="department" required value="{{ old('department', $employee->department) }}" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Job Title</label>
                    <input type="text" name="position" required value="{{ old('position', $employee->position) }}" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Basic Salary (PHP)</label>
                    <input type="number" step="0.01" name="basic_salary" required value="{{ old('basic_salary', $employee->basic_salary) }}" class="sigla-input w-full text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status</label>
                    <select name="status" class="sigla-input w-full text-xs">
                        <option value="active" {{ $employee->status === 'active' ? 'selected' : '' }} class="bg-black text-white">Active</option>
                        <option value="inactive" {{ $employee->status === 'inactive' ? 'selected' : '' }} class="bg-black text-white">Inactive</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Hire Date</label>
                    <input type="date" name="hire_date" value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}" class="sigla-input w-full text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Reset Password (Leave blank to keep)</label>
                <input type="password" name="password" placeholder="New password..." class="sigla-input w-full text-xs">
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-green-800">
                <a href="{{ route('employees.show', $employee) }}" class="px-4 py-2 text-xs font-mono text-slate-400 hover:text-white">
                    Cancel
                </a>
                <button type="submit" class="btn-sigla-green text-xs !py-2.5 !px-6">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
