<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siglatrolap Innovation Inc</title>
    <link rel="shortcut icon" href="{{ asset('images/Icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-between antialiased">
    <!-- Header matching legacy land-page.html -->
    <header class="sigla-header sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/Iconwhite.png') }}" alt="Logo Image" class="h-10 w-auto">
                <span class="font-extrabold text-xl tracking-wider text-white">Siglatrolap Innovation Inc</span>
            </div>
            <nav class="hidden md:flex items-center sigla-nav space-x-4">
                <a href="#features">Features</a>
                <a href="{{ route('scanner') }}" class="text-green-400 border border-green-500 rounded px-3 py-1 hover:bg-green-500 hover:text-black">
                    <i class="fas fa-qrcode mr-1"></i> QR Kiosk
                </a>
                <a href="{{ route('login') }}" class="btn-sigla-primary text-xs !py-1.5 !px-4">Login</a>
                <a href="{{ route('register') }}" class="btn-sigla-green text-xs !py-1.5 !px-4">Sign Up</a>
            </nav>
        </div>
    </header>

    <!-- Tagline Banner -->
    <div class="text-center py-10 px-4">
        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-widest text-white uppercase drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)]">
            THE SPARK THAT <span class="solo-one">TRANSFORM</span> IDEAS TO <span class="solo-two">REALITY</span>
        </h1>
    </div>

    <!-- Sections 1, 2, 3 matching legacy structure -->
    <main class="max-w-5xl mx-auto px-4 py-8 space-y-12 flex-1">
        <!-- 1st Section: Attendance -->
        <section id="features" class="sigla-card p-8 sm:p-12 text-center space-y-6">
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-wide uppercase">
                <span style="color: rgb(0, 187, 255);">EASY </span>TO <span style="color: rgb(0, 255, 0);">MANAGE </span>YOUR<br>
                DAILY <span style="color: rgb(0, 187, 255);">TIME IN </span>AND <span style="color: rgb(0, 255, 0);">TIME OUT</span>
            </h2>
            <p class="text-slate-300 max-w-xl mx-auto text-base">
                Instant biometric QR code verification with automated tardiness and overtime detection.
            </p>
            <div class="flex flex-wrap justify-center gap-4 pt-2">
                <a href="{{ route('scanner') }}" class="btn-sigla-green inline-flex items-center">
                    <i class="fas fa-camera mr-2"></i> Open QR Scanner Kiosk
                </a>
                <a href="{{ route('login') }}" class="btn-sigla-primary inline-flex items-center">
                    <i class="fas fa-sign-in-alt mr-2"></i> Employee Portal Login
                </a>
            </div>
        </section>

        <!-- 2nd Section: Payroll -->
        <section class="sigla-card p-8 sm:p-12 text-center space-y-6">
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-wide uppercase">
                YOU CAN ABLE TO ACCESS YOUR <span style="color: rgb(0, 255, 0);">PAYROLL </span><br>
                TO SEE YOUR <span style="color: #ff3333;">EARNINGS</span>
            </h2>
            <p class="text-slate-300 max-w-xl mx-auto text-base">
                Automated monthly salary calculation, hourly rate breakdown, and official downloadable printable payslips.
            </p>
            <div class="pt-2">
                <a href="{{ route('login') }}" class="btn-sigla-primary inline-flex items-center">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> View Sample Payslip
                </a>
            </div>
        </section>

        <!-- 3rd Section: Live Notifications -->
        <section class="sigla-card p-8 sm:p-12 text-center space-y-4">
            <h2 class="text-xl sm:text-3xl font-black uppercase text-white tracking-wider">
                INFORMING YOUR DAILY TIME RECORD
            </h2>
            <h3 class="text-lg sm:text-2xl font-bold uppercase" style="color: rgb(0, 255, 0);">
                ACCURATELY WITH REAL-TIME TIMESTAMPS
            </h3>
        </section>
    </main>

    <!-- Footer matching legacy footer -->
    <footer class="footer mt-auto max-w-7xl mx-auto w-full px-6 py-8 text-center text-xs">
        <hr style="border: none; border-top: 1px solid #00ff11; margin-bottom: 20px;">
        <div class="space-y-2 font-mono text-slate-300">
            <h3 class="text-sm font-bold" style="color: #00ff00;">All Rights Reserved @ {{ date('Y') }} Siglatrolap Innovations Inc</h3>
            <p class="text-slate-400">Need Help? Contact Us: <span style="color: #00ff00;">siglatrolapinnovations@gmail.com</span></p>
        </div>
    </footer>
</body>
</html>
