<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Siglatrolap Innovations Inc') }} - @yield('title', 'Attendance & Payroll')</title>
    <link rel="shortcut icon" href="{{ asset('images/Icon.png') }}" type="image/x-icon">

    <!-- Font Awesome (Original Legacy Suite) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-full flex flex-col antialiased">
    <div class="min-h-full flex flex-col">
        <!-- Top Legacy Style Header -->
        <header class="sigla-header sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Brand & Consistent Logo -->
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 text-white no-underline">
                            <img src="{{ asset('images/Iconwhite.png') }}" alt="Siglatrolap Logo" class="h-12 w-auto object-contain drop-shadow-[0_0_8px_rgba(0,255,0,0.5)]">
                            <span class="hidden sm:inline-block font-extrabold text-xl tracking-wider text-white">
                                Siglatrolap <span style="color: rgb(0, 255, 0);">Innovations</span>
                            </span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center sigla-nav space-x-2">
                        @if(Auth::check())
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="fa fa-home mr-1"></i> HOME
                            </a>

                            @if(Auth::user()->isEmployee())
                                <a href="{{ route('attendances.index') }}" class="{{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-list-check mr-1"></i> ATTENDANCE
                                </a>
                                <a href="{{ route('payrolls.index') }}" class="{{ request()->routeIs('payrolls.*') ? 'active' : '' }}">
                                    <i class="fa fa-money-bill-wave mr-1"></i> PAYSLIP
                                </a>
                                <a href="{{ route('leaves.index') }}" class="{{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                                    <i class="fa fa-calendar-plus mr-1"></i> SUBMIT LEAVE
                                </a>
                            @else
                                <a href="{{ route('attendances.index') }}" class="{{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-clipboard-user mr-1"></i> RECORDS
                                </a>
                                <a href="{{ route('payrolls.index') }}" class="{{ request()->routeIs('payrolls.*') ? 'active' : '' }}">
                                    <i class="fa fa-wallet mr-1"></i> MANAGE PAYSLIP
                                </a>
                                <a href="{{ route('leaves.index') }}" class="{{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                                    <i class="fa fa-envelope-open-text mr-1"></i> LEAVE REQUEST
                                </a>
                                <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                    <i class="fa fa-users mr-1"></i> EMPLOYEES
                                </a>
                                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                    <i class="fa fa-cogs mr-1"></i> SETTINGS
                                </a>
                            @endif

                            <!-- Kiosk Terminal Quick Link -->
                            <a href="{{ route('scanner') }}" target="_blank" class="text-xs px-2.5 py-1 rounded border border-green-500 text-green-400 hover:bg-green-500 hover:text-black transition">
                                <i class="fas fa-qrcode mr-1"></i> QR KIOSK
                            </a>

                            <!-- Notification Bell -->
                            <a href="{{ route('leaves.index') }}" title="Notifications / Status" style="color: rgb(0, 255, 0); font-size: 20px; padding: 4px 8px;">
                                🔔
                            </a>

                            <!-- User Profile & Sign Out -->
                            <div class="flex items-center space-x-3 pl-3 border-l border-green-900/50">
                                <a href="{{ route('profile') }}" class="flex items-center space-x-2 text-xs text-slate-300 hover:text-white" title="My Profile">
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border-2 border-green-500">
                                    <span class="hidden lg:inline text-green-400 font-bold">{{ Auth::user()->username ?? Auth::user()->name }}</span>
                                </a>

                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded border border-red-500/80 text-red-400 hover:bg-red-600 hover:text-white transition">
                                        <i class="fa fa-sign-out-alt"></i> LOGOUT
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}">LOGIN</a>
                            <a href="{{ route('register') }}">SIGN UP</a>
                        @endif
                    </nav>

                    <!-- Mobile Menu Bar Button -->
                    <div class="md:hidden flex items-center space-x-2">
                        <a href="{{ route('scanner') }}" class="text-xs px-2 py-1 border border-green-500 text-green-400 rounded">QR</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs px-2 py-1 border border-red-500 text-red-400 rounded">Exit</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Mobile Links Sub-bar -->
            <div class="md:hidden flex overflow-x-auto px-4 py-2 space-x-3 bg-black/90 border-t border-green-900/40 text-xs sigla-nav">
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Home</a>
                <a href="{{ route('attendances.index') }}" class="{{ request()->routeIs('attendances.*') ? 'active' : '' }}">Attendance</a>
                <a href="{{ route('payrolls.index') }}" class="{{ request()->routeIs('payrolls.*') ? 'active' : '' }}">Payslip</a>
                <a href="{{ route('leaves.index') }}" class="{{ request()->routeIs('leaves.*') ? 'active' : '' }}">Leaves</a>
                @if(Auth::check() && Auth::user()->isAdmin())
                    <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">Employees</a>
                    <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">Settings</a>
                @endif
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            @if(session('success'))
                <div class="p-4 mb-4 rounded-xl sigla-card border-green-500 text-green-300 text-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-green-400 text-lg"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <span class="text-xs text-green-500 font-mono">OK</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 rounded-xl sigla-card border-red-500 text-red-300 text-sm flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-exclamation-triangle text-red-400 text-lg"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <span class="text-xs text-red-500 font-mono">ERROR</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 mb-4 rounded-xl sigla-card border-red-500 text-red-300 text-sm">
                    <p class="font-bold text-red-400 mb-1"><i class="fas fa-times-circle mr-1"></i> Please check the following:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main Workspace -->
        <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
            @yield('content')
        </main>

        <!-- Legacy Themed Footer -->
        <footer class="footer mt-auto max-w-7xl mx-auto w-full px-4 py-8 text-center text-xs">
            <hr style="border: none; border-top: 1px solid #00ff11; margin-bottom: 20px;">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 text-slate-400 font-mono">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/Iconwhite.png') }}" alt="Logo" class="w-5 h-auto">
                    <span style="color: #00ff00; font-weight: bold;">&copy; {{ date('Y') }} Siglatrolap Innovations Inc</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('home') }}" style="color: #00ff00; text-decoration: none;">Features</a>
                    <a href="{{ route('scanner') }}" style="color: #00ff00; text-decoration: none;">QR Kiosk</a>
                    <a href="{{ route('attendances.index') }}" style="color: #00ff00; text-decoration: none;">Time Record</a>
                    <span class="text-slate-500 font-sans">siglatrolapinnovations@gmail.com</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Light-Dismiss Fallback Polyfill for HTML Dialogs per modern web guidance -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dialogs = document.querySelectorAll('dialog');
            dialogs.forEach(dialog => {
                if (!('closedBy' in HTMLDialogElement.prototype)) {
                    dialog.addEventListener('click', (event) => {
                        if (event.target !== dialog) return;
                        const rect = dialog.getBoundingClientRect();
                        const isDialogContent = (
                            rect.top <= event.clientY &&
                            event.clientY <= rect.top + rect.height &&
                            rect.left <= event.clientX &&
                            event.clientX <= rect.left + rect.width
                        );
                        if (!isDialogContent) {
                            dialog.close();
                        }
                    });
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
