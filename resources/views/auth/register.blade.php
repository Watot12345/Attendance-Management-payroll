<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siglatrolap Innovation Signup</title>
    <link rel="shortcut icon" href="{{ asset('images/Icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-center items-center p-4">
    <div class="max-w-xl w-full my-auto space-y-6">
        <!-- Logo Section matching legacy logo-section -->
        <div class="text-center">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/Logo.png') }}" alt="Company Logo" class="w-48 mx-auto drop-shadow-[0_0_12px_rgba(0,0,0,0.8)]">
            </a>
            <h1 class="text-xl font-bold tracking-wider uppercase mt-2 text-white">Create Employee Account</h1>
        </div>

        @if($errors->any())
            <div class="sigla-card p-4 border-red-500 text-red-300 text-xs text-center font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="sigla-card p-6 sm:p-8 space-y-4">
            <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-semibold text-white mb-1">Full Name</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-id-card absolute left-3 text-green-400"></i>
                            <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Full Name..." class="sigla-input w-full pl-9 text-xs">
                        </div>
                    </div>
                    <div>
                        <label for="username" class="block text-xs font-semibold text-white mb-1">Username</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-user absolute left-3 text-green-400"></i>
                            <input type="text" id="username" name="username" required value="{{ old('username') }}" placeholder="Username..." class="sigla-input w-full pl-9 text-xs">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-white mb-1">Email Address</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-envelope absolute left-3 text-green-400"></i>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="Email..." class="sigla-input w-full pl-9 text-xs">
                        </div>
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-semibold text-white mb-1">Phone Number</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-phone absolute left-3 text-green-400"></i>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Phone..." class="sigla-input w-full pl-9 text-xs">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="department" class="block text-xs font-semibold text-white mb-1">Department</label>
                        <input type="text" id="department" name="department" value="{{ old('department', 'Operations') }}" placeholder="Department..." class="sigla-input w-full text-xs">
                    </div>
                    <div>
                        <label for="position" class="block text-xs font-semibold text-white mb-1">Job Title</label>
                        <input type="text" id="position" name="position" value="{{ old('position', 'Staff Member') }}" placeholder="Position..." class="sigla-input w-full text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-white mb-1">Password</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-lock absolute left-3 text-green-400"></i>
                            <input type="password" id="password" name="password" required placeholder="Password..." class="sigla-input w-full pl-9 text-xs">
                        </div>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-white mb-1">Confirm Password</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-lock absolute left-3 text-green-400"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Confirm Password..." class="sigla-input w-full pl-9 text-xs">
                        </div>
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="btn-sigla-primary w-full py-3 text-xs tracking-wider uppercase">
                        <i class="fas fa-user-plus mr-2"></i> Create Account & Generate QR Pass
                    </button>
                </div>

                <p class="text-center text-xs text-slate-300 pt-2">
                    Already registered?
                    <a href="{{ route('login') }}" class="text-green-400 font-bold hover:underline ml-1">Sign In</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
