@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-3 border-b border-[#00ff00]/40 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-wide uppercase drop-shadow-[0_0_8px_#00ff00]">
                <i class="fa-solid fa-id-card-clip text-[#00ff00] mr-2"></i> Account & Profile
            </h1>
            <p class="text-xs text-gray-300 font-sans mt-1">Manage your personal credentials, contact information, and attendance pass.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Left: Profile Info and Passwords -->
        <div class="md:col-span-7 space-y-6">
            <!-- Edit Profile Form -->
            <div class="sigla-card rounded-2xl p-6 space-y-4">
                <h2 class="text-sm font-bold text-[#00ff00] uppercase tracking-wider pb-3 border-b border-gray-700/60 font-sans flex items-center">
                    <i class="fa-solid fa-user-pen mr-2 text-[rgb(0,187,255)]"></i> Personal Information
                </h2>

                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4 font-sans text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-semibold text-gray-300 mb-1">Full Name</label>
                        <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                               class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-300 mb-1">Email Address</label>
                        <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                               class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block font-semibold text-gray-300 mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0912 345 6789"
                               class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-sigla-green px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-[0_0_12px_rgba(0,255,0,0.3)]">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="sigla-card rounded-2xl p-6 space-y-4">
                <h2 class="text-sm font-bold text-[#00ff00] uppercase tracking-wider pb-3 border-b border-gray-700/60 font-sans flex items-center">
                    <i class="fa-solid fa-key mr-2 text-[rgb(0,187,255)]"></i> Change Password
                </h2>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4 font-sans text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block font-semibold text-gray-300 mb-1">Current Password</label>
                        <input type="password" name="current_password" required
                               class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-gray-300 mb-1">New Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-300 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation" required
                                   class="w-full px-3 py-2 bg-black/70 border-2 border-gray-700 rounded-xl text-white text-xs focus:border-[#00ff00] focus:outline-none transition">
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn-sigla-primary px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right: Digital QR Badge Card -->
        <div class="md:col-span-5 space-y-6">
            <div class="sigla-card rounded-2xl p-6 text-white shadow-2xl flex flex-col items-center justify-between text-center space-y-4 border-2 border-[#00ff00]">
                <div class="w-full flex justify-between items-center pb-3 border-b border-gray-700/60">
                    <div class="flex items-center space-x-2">
                        <img src="{{ asset('images/Iconwhite.png') }}" alt="Siglatrolap" class="h-6 w-auto">
                        <span class="text-xs uppercase tracking-widest font-extrabold text-[#00ff00]">SIGLA PASS</span>
                    </div>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-[#00ff00]/20 text-[#00ff00] font-bold border border-[#00ff00]/40">{{ strtoupper($user->role) }}</span>
                </div>

                <div class="p-3 bg-white rounded-2xl shadow-[0_0_20px_rgba(0,255,0,0.3)]">
                    <canvas id="userQrCanvas" class="rounded-xl mx-auto"></canvas>
                </div>

                <div>
                    <h3 class="text-lg font-bold text-white uppercase tracking-wide">{{ $user->name }}</h3>
                    <p class="text-xs text-[rgb(0,187,255)] font-sans font-semibold">{{ $user->position ?? 'Staff Member' }}</p>
                    <p class="text-[11px] text-gray-400 font-sans">{{ $user->department ?? 'Operations' }}</p>
                </div>

                <div class="p-2.5 bg-black/80 rounded-xl border border-gray-700 text-[10px] font-mono text-gray-300 break-all w-full select-all">
                    Token: {{ $user->qr_secret }}
                </div>

                <p class="text-[11px] text-gray-400 font-sans">
                    Present this pass to the office kiosk scanner or security terminal to record time in/out.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (window.QRCode) {
            const qrCanvas = document.getElementById('userQrCanvas');
            const secret = "{{ $user->qr_secret }}";
            window.QRCode.toCanvas(qrCanvas, secret, { width: 180, margin: 2, color: { dark: '#0f172a', light: '#ffffff' } }, function (err) {
                if (err) console.error(err);
            });
        }
    });
</script>
@endpush
