@extends('layouts.app')

@section('title', 'Employee Directory')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-green-800">
        <div>
            <h1 class="text-2xl font-black text-white tracking-wider uppercase">
                <i class="fas fa-users text-green-400 mr-2"></i> Employee Directory
            </h1>
            <p class="text-xs text-slate-400 font-mono mt-1">Manage personnel, digital QR passes, and compensation records.</p>
        </div>
        <div>
            <a href="{{ route('employees.create') }}" class="btn-sigla-green text-xs !py-2 !px-4 inline-flex items-center">
                <i class="fas fa-user-plus mr-1.5"></i> Register New Employee
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="sigla-card p-4">
        <form method="GET" action="{{ route('employees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div class="lg:col-span-2">
                <label class="block font-semibold text-slate-300 mb-1">Search Personnel</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, role, or position..."
                       class="sigla-input w-full text-xs">
            </div>

            <div>
                <label class="block font-semibold text-slate-300 mb-1">Department</label>
                <select name="department" class="sigla-input w-full text-xs">
                    <option value="" class="bg-black text-white">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }} class="bg-black text-white">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-sigla-green flex-1 text-xs !py-2">
                    Filter
                </button>
                <a href="{{ route('employees.index') }}" class="btn-sigla-primary text-xs !py-2 text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Employees Grid matching legacy .employees-grid and .employee-card -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($employees as $employee)
            <div class="sigla-card p-6 flex flex-col justify-between space-y-4 hover:border-green-400 transition">
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}"
                             class="w-16 h-16 rounded-full object-cover border-2 border-green-500 shadow-md">
                        <div class="flex-1">
                            <h2 class="font-bold text-white text-base leading-tight">{{ $employee->name }}</h2>
                            <p class="text-xs text-green-400 font-mono">{{ $employee->position }}</p>
                            <span class="inline-block mt-1 font-mono text-[10px] px-2 py-0.5 rounded {{ $employee->status === 'active' ? 'bg-green-500/20 text-green-300 border border-green-500/40' : 'bg-red-500/20 text-red-300 border border-red-500/40' }}">
                                {{ strtoupper($employee->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-3 bg-black/60 rounded-xl border border-green-950 text-xs font-mono space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Department:</span>
                            <span class="text-white font-bold">{{ $employee->department }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Monthly Basic:</span>
                            <span class="text-green-400">&#8369;{{ number_format($employee->basic_salary, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Email:</span>
                            <span class="text-slate-300 truncate max-w-[150px]">{{ $employee->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-green-900/40 flex justify-between items-center text-xs">
                    <a href="{{ route('employees.show', $employee) }}" class="employee-button !py-1 !px-3">
                        <i class="fas fa-qrcode mr-1"></i> Profile & QR Pass
                    </a>
                    <a href="{{ route('employees.edit', $employee) }}" class="text-slate-400 hover:text-green-400 transition font-mono">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full sigla-card p-12 text-center text-slate-400 font-mono">
                No employees found.
            </div>
        @endforelse
    </div>

    <div class="pt-4 border-t border-green-900/40">
        {{ $employees->links() }}
    </div>
</div>
@endsection
