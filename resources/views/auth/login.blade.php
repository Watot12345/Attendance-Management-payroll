<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Siglatrolap Innovation Login</title>
    <link rel="shortcut icon" href="{{ asset('images/Icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-center items-center p-4">
    <div class="max-w-md w-full my-auto space-y-6">
        <!-- Logo Section matching legacy logo-section -->
        <div class="text-center">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/Logo.png') }}" alt="Company Logo" class="w-48 mx-auto drop-shadow-[0_0_12px_rgba(0,0,0,0.8)]">
            </a>
        </div>

        <!-- Login Options Toggle matching legacy .login-options -->
        <div class="flex justify-center space-x-3 text-xs">
            <button id="manualLoginBtn" type="button" onclick="switchLoginMode('manual')"
                    class="px-5 py-2.5 rounded font-bold transition flex items-center bg-green-500 text-black">
                <i class="fas fa-keyboard mr-2"></i> Manual Login
            </button>
            <button id="qrLoginBtn" type="button" onclick="switchLoginMode('qr')"
                    class="px-5 py-2.5 rounded font-bold transition flex items-center bg-stone-900/90 text-white border border-stone-700 hover:border-green-500">
                <i class="fas fa-qrcode mr-2"></i> QR Code Login
            </button>
        </div>

        @if(session('error'))
            <p style="color: #ff3333; text-align: center; font-weight: bold;" class="text-sm">
                {{ session('error') }}
            </p>
        @endif

        @if($errors->any())
            <p style="color: #ff3333; text-align: center; font-weight: bold;" class="text-sm">
                {{ $errors->first() }}
            </p>
        @endif

        @if(session('success'))
            <p style="color: rgb(0, 255, 0); text-align: center; font-weight: bold;" class="text-sm">
                {{ session('success') }}
            </p>
        @endif

        <!-- Manual Login Form Container matching legacy .form-styling -->
        <div id="manualLoginForm" class="sigla-card p-6 sm:p-8 space-y-5">
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Username/Email -->
                <div class="relative">
                    <label for="login" class="block text-xs font-semibold text-white mb-1">Username or Email</label>
                    <div class="relative flex items-center">
                        <i class="fas fa-user absolute left-3 text-green-400"></i>
                        <input type="text" id="login" name="login" required autofocus value="{{ old('login', 'admin@siglatrolap.com') }}"
                               placeholder="Enter Username or Email..." class="sigla-input w-full pl-9 text-xs">
                    </div>
                </div>

                <!-- Password -->
                <div class="relative">
                    <label for="password" class="block text-xs font-semibold text-white mb-1">Password</label>
                    <div class="relative flex items-center">
                        <i class="fas fa-lock absolute left-3 text-green-400"></i>
                        <input type="password" id="password" name="password" required value="password"
                               placeholder="Enter Password..." class="sigla-input w-full pl-9 pr-10 text-xs">
                        <i class="fas fa-eye-slash absolute right-3 cursor-pointer text-green-400" id="togglePassword"></i>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-300 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-700 bg-black text-green-500 focus:ring-green-500">
                        <span class="ml-2 font-mono text-[11px]">Remember me</span>
                    </label>
                    <a href="{{ route('scanner') }}" class="text-green-400 hover:underline font-bold text-xs">
                        QR Kiosk Terminal &rarr;
                    </a>
                </div>

                <!-- Submit Button matching legacy form-button button -->
                <div class="pt-2">
                    <button type="submit" class="btn-sigla-primary w-full py-3 text-xs tracking-wider uppercase">
                        <i class="fas fa-sign-in-alt mr-2"></i> Log In
                    </button>
                </div>

                <hr style="border: none; border-top: 1px solid rgba(255, 255, 255, 0.2); margin: 15px 0;">

                <p class="text-center text-xs text-slate-300">
                    <i class="fas fa-user-check text-green-400 mr-1"></i> Not Registered?
                    <a href="{{ route('register') }}" class="text-green-400 font-bold hover:underline ml-1">Create Account</a>
                </p>
            </form>

            <!-- Demo Helper Autofill (styled in theme) -->
            <div class="pt-4 border-t border-green-900/40 text-center text-xs">
                <span class="text-[11px] font-mono text-green-400 block mb-2 font-bold uppercase tracking-wider">Demo Credentials</span>
                <div class="grid grid-cols-2 gap-2 text-xs font-mono">
                    <button type="button" onclick="fillCreds('admin@siglatrolap.com', 'password')" class="employee-button text-center">
                        Admin Login
                    </button>
                    <button type="button" onclick="fillCreds('joshua@gmail.com', 'password')" class="employee-button text-center">
                        Employee Login
                    </button>
                </div>
            </div>
        </div>

        <!-- In-Browser QR Login Form matching legacy #qrLoginForm -->
        <div id="qrLoginForm" class="sigla-card p-6 hidden space-y-4 text-center">
            <h3 class="font-bold text-white text-sm" style="color: #00ff00;">
                <i class="fas fa-qrcode mr-2"></i> Scan Your Personal Pass
            </h3>
            <p class="text-xs text-slate-300">Hold your Siglatrolap QR Pass up to your camera to sign in.</p>

            <div id="loginReader" class="w-full rounded-xl overflow-hidden"></div>
            <div id="loginQrMsg" class="text-xs font-bold font-mono"></div>
        </div>
    </div>

    <!-- Script for toggle and scanner -->
    <script>
        function fillCreds(u, p) {
            document.getElementById('login').value = u;
            document.getElementById('password').value = p;
        }

        // Password eye toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        let loginScanner = null;

        function switchLoginMode(mode) {
            const manualBtn = document.getElementById('manualLoginBtn');
            const qrBtn = document.getElementById('qrLoginBtn');
            const manualForm = document.getElementById('manualLoginForm');
            const qrForm = document.getElementById('qrLoginForm');

            if (mode === 'manual') {
                manualBtn.className = 'px-5 py-2.5 rounded font-bold transition flex items-center bg-green-500 text-black';
                qrBtn.className = 'px-5 py-2.5 rounded font-bold transition flex items-center bg-stone-900/90 text-white border border-stone-700 hover:border-green-500';
                manualForm.classList.remove('hidden');
                qrForm.classList.add('hidden');
                if (loginScanner) {
                    loginScanner.clear();
                }
            } else {
                qrBtn.className = 'px-5 py-2.5 rounded font-bold transition flex items-center bg-green-500 text-black';
                manualBtn.className = 'px-5 py-2.5 rounded font-bold transition flex items-center bg-stone-900/90 text-white border border-stone-700 hover:border-green-500';
                manualForm.classList.add('hidden');
                qrForm.classList.remove('hidden');

                if (window.Html5QrcodeScanner) {
                    loginScanner = new window.Html5QrcodeScanner("loginReader", { fps: 10, qrbox: 220 });
                    loginScanner.render((qrCode) => {
                        document.getElementById('loginQrMsg').innerHTML = '<span style="color: rgb(0, 255, 0);">QR Verified! Logging in...</span>';
                        // Auto authenticate using verified user secret
                        fetch("{{ route('scanner.verify') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({ qr_secret: qrCode, action: 'auto' })
                        })
                        .then(() => {
                            window.location.href = "{{ route('dashboard') }}";
                        })
                        .catch(() => {
                            document.getElementById('loginQrMsg').innerHTML = '<span style="color: #ff3333;">Invalid QR Code</span>';
                        });
                    });
                }
            }
        }
    </script>
</body>
</html>
