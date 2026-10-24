<!DOCTYPE html>
<html lang="en" class="h-full bg-black text-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Siglatrolap - Biometric QR Attendance Kiosk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: linear-gradient(rgba(0,0,0,0.82), rgba(0,0,0,0.82)), url('{{ asset("images/bg-img.jpg") }}') center/cover fixed no-repeat;
        }
    </style>
</head>
<body class="h-full flex flex-col font-serif antialiased selection:bg-[#00ff00] selection:text-black">
    <!-- Kiosk Top Navigation Bar -->
    <header class="bg-black/90 border-b-2 border-[#00ff00] px-6 py-3 flex justify-between items-center z-10 backdrop-blur-md shadow-[0_4px_20px_rgba(0,255,0,0.15)]">
        <div class="flex items-center space-x-4">
            <a href="{{ route('home') }}" class="flex items-center space-x-3">
                <img src="{{ asset('images/Iconwhite.png') }}" alt="Siglatrolap Logo" class="h-10 w-auto">
                <div>
                    <h1 class="text-xl font-bold tracking-wider text-white drop-shadow-[0_0_8px_#00ff00]">Siglatrolap Kiosk</h1>
                    <p class="text-[10px] text-[rgb(0,187,255)] uppercase tracking-widest font-sans">Contactless Biometric QR Kiosk Terminal</p>
                </div>
            </a>
        </div>

        <!-- Live Clock Display -->
        <div class="text-center px-4 py-1 rounded-xl bg-black/60 border border-[#00ff00]/40">
            <div id="kioskClock" class="text-2xl font-black font-mono tracking-widest text-[#00ff00] drop-shadow-[0_0_8px_#00ff00]">00:00:00</div>
            <div class="text-[10px] text-gray-300 font-sans tracking-wide uppercase">{{ \Carbon\Carbon::today()->format('l, F j, Y') }}</div>
        </div>

        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-black bg-[#00ff00] hover:bg-emerald-400 rounded-lg shadow-[0_0_12px_rgba(0,255,0,0.4)] transition">
                    &larr; Back to Portal
                </a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider text-white bg-[rgb(0,187,255)]/20 hover:bg-[rgb(0,187,255)]/40 border border-[rgb(0,187,255)] rounded-lg transition">
                    Sign In
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Scanner Work Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left Column: Camera Viewport -->
        <div class="lg:col-span-7 space-y-4">
            <!-- Mode Selector Bar -->
            <div class="p-2.5 sigla-card rounded-2xl flex items-center justify-between text-xs font-sans">
                <span class="text-gray-300 font-bold px-2 uppercase tracking-wider text-[11px]">Scan Mode:</span>
                <div class="flex space-x-2" id="modeButtons">
                    <button type="button" onclick="setScanMode('auto')" id="btn-mode-auto" class="px-3.5 py-1.5 rounded-lg font-bold bg-[#00ff00] text-black shadow-[0_0_10px_rgba(0,255,0,0.5)] transition uppercase text-xs">
                        Auto (In/Out)
                    </button>
                    <button type="button" onclick="setScanMode('in')" id="btn-mode-in" class="px-3.5 py-1.5 rounded-lg font-bold bg-black/60 text-gray-300 border border-gray-700 hover:border-[#00ff00] transition uppercase text-xs">
                        Time In Only
                    </button>
                    <button type="button" onclick="setScanMode('out')" id="btn-mode-out" class="px-3.5 py-1.5 rounded-lg font-bold bg-black/60 text-gray-300 border border-gray-700 hover:border-[#00ff00] transition uppercase text-xs">
                        Time Out Only
                    </button>
                </div>
            </div>

            <!-- Viewport Container -->
            <div class="relative bg-black rounded-2xl border-2 border-[#00ff00] shadow-[0_0_30px_rgba(0,255,0,0.2)] overflow-hidden aspect-video sm:aspect-[4/3] flex items-center justify-center">
                <div id="reader" class="w-full h-full"></div>

                <!-- Overlay Scan Reticle -->
                <div id="scannerOverlay" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center">
                    <div class="w-64 h-64 border-2 border-[#00ff00]/60 rounded-2xl relative shadow-[0_0_35px_rgba(0,255,0,0.3)]">
                        <div class="absolute -top-1 -left-1 w-7 h-7 border-t-4 border-l-4 border-[#00ff00] rounded-tl-lg"></div>
                        <div class="absolute -top-1 -right-1 w-7 h-7 border-t-4 border-r-4 border-[#00ff00] rounded-tr-lg"></div>
                        <div class="absolute -bottom-1 -left-1 w-7 h-7 border-b-4 border-l-4 border-[#00ff00] rounded-bl-lg"></div>
                        <div class="absolute -bottom-1 -right-1 w-7 h-7 border-b-4 border-r-4 border-[#00ff00] rounded-br-lg"></div>
                        <!-- Scanning Laser Line -->
                        <div class="w-full h-0.5 bg-gradient-to-r from-transparent via-[#00ff00] to-transparent absolute top-1/2 -translate-y-1/2 animate-pulse shadow-[0_0_10px_#00ff00]"></div>
                    </div>
                    <p class="text-xs font-bold text-white mt-4 tracking-widest uppercase drop-shadow-[0_0_8px_#00ff00] font-sans">
                        Align QR Pass Inside the Frame
                    </p>
                </div>

                <!-- Camera Controls Footer -->
                <div class="absolute bottom-3 left-3 right-3 flex justify-between items-center bg-black/85 backdrop-blur-md px-4 py-2 rounded-xl border border-[#00ff00]/40 text-xs font-sans">
                    <div class="flex items-center space-x-2 text-gray-200">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#00ff00] animate-ping"></span>
                        <span id="cameraStatus" class="font-bold text-[#00ff00]">Camera Active</span>
                    </div>
                    <select id="cameraSelect" class="bg-black text-gray-200 text-xs border border-gray-700 rounded-lg px-2 py-1 focus:border-[#00ff00] focus:outline-none">
                        <option value="">Detecting Cameras...</option>
                    </select>
                </div>
            </div>

            <!-- Manual Barcode / Secret Entry Fallback -->
            <div class="p-4 sigla-card rounded-2xl">
                <form id="manualEntryForm" class="flex gap-2" onsubmit="event.preventDefault(); submitQrSecret(document.getElementById('manualSecretInput').value);">
                    <input type="text" id="manualSecretInput" placeholder="Manual QR Token or USB Barcode Scanner..."
                           class="flex-1 px-4 py-2.5 rounded-xl bg-black/70 border-2 border-gray-700 focus:border-[#00ff00] text-white placeholder-gray-500 text-xs focus:outline-none transition">
                    <button type="submit" class="px-5 py-2.5 bg-[#00ff00] hover:bg-emerald-400 text-black text-xs font-bold uppercase tracking-wider rounded-xl transition shadow-[0_0_12px_rgba(0,255,0,0.3)]">
                        Verify Code
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column: Verification Result Card & Recent Scan Feed -->
        <div class="lg:col-span-5 space-y-4">
            <!-- Dynamic Result Card -->
            <div id="resultCard" class="p-6 rounded-2xl sigla-card shadow-2xl transition-all duration-300 hidden">
                <div class="flex items-center space-x-4">
                    <img id="resultAvatar" src="" alt="Avatar" class="w-16 h-16 rounded-xl object-cover border-2 border-[#00ff00] shadow-[0_0_12px_rgba(0,255,0,0.5)]">
                    <div class="flex-1">
                        <div id="resultStatusBadge" class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1 font-sans"></div>
                        <h2 id="resultName" class="text-xl font-bold text-white leading-tight"></h2>
                        <p id="resultPosition" class="text-xs text-gray-300 font-sans"></p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-700/60 grid grid-cols-2 gap-3 text-xs font-sans">
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Action</span>
                        <span id="resultActionText" class="font-bold text-[#00ff00] text-sm uppercase"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Timestamp</span>
                        <span id="resultTimeText" class="font-bold text-white text-sm font-mono"></span>
                    </div>
                </div>

                <div id="resultMessage" class="mt-4 p-3 rounded-xl text-xs font-semibold font-sans"></div>
            </div>

            <!-- Standby Placeholder when no scan recently -->
            <div id="standbyCard" class="p-8 rounded-2xl sigla-card text-center space-y-3">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-black/80 border-2 border-[#00ff00] flex items-center justify-center text-[#00ff00] shadow-[0_0_15px_rgba(0,255,0,0.25)]">
                    <i class="fa-solid fa-qrcode text-2xl"></i>
                </div>
                <h3 class="font-bold text-white text-lg uppercase tracking-wide">Terminal Ready</h3>
                <p class="text-xs text-gray-300 max-w-xs mx-auto font-sans leading-relaxed">
                    Present your digital pass or employee badge facing the camera to automatically log attendance.
                </p>
            </div>

            <!-- Recent Scans Ticker -->
            <div class="p-5 sigla-card rounded-2xl space-y-3">
                <div class="flex justify-between items-center pb-2 border-b border-gray-700/60">
                    <h4 class="text-xs font-bold text-[#00ff00] uppercase tracking-wider font-sans"><i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Today's Recent Scans</h4>
                    <span class="text-[10px] text-[#00ff00] font-mono animate-pulse">● LIVE</span>
                </div>

                <div id="recentScansList" class="space-y-2 max-h-80 overflow-y-auto pr-1 text-xs font-sans">
                    @forelse($recentScans as $scan)
                        <div class="p-2.5 rounded-xl bg-black/60 border border-gray-800 hover:border-[#00ff00]/60 transition flex items-center justify-between">
                            <div class="flex items-center space-x-2.5">
                                <img src="{{ $scan->user->avatar_url }}" alt="{{ $scan->user->name }}" class="w-8 h-8 rounded-full object-cover border border-[#00ff00]/40">
                                <div>
                                    <div class="font-bold text-white text-xs">{{ $scan->user->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $scan->user->department }}</div>
                                </div>
                            </div>
                            <div class="text-right font-mono">
                                <div class="text-[#00ff00] font-bold">{{ $scan->check_out ? 'OUT: ' . \Carbon\Carbon::parse($scan->check_out)->format('h:i A') : 'IN: ' . \Carbon\Carbon::parse($scan->check_in)->format('h:i A') }}</div>
                                <div class="text-[10px] text-gray-400 uppercase">{{ $scan->status }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-xs">
                            No scans recorded yet today.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>

    <script>
        let currentScanMode = 'auto';
        let isProcessing = false;
        let html5QrCode = null;

        // Audio synthesis for instant chime feedback (no external assets required!)
        function playChime(isSuccess = true) {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (isSuccess) {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                    osc.frequency.setValueAtTime(880, ctx.currentTime + 0.1); // A5
                    gain.gain.setValueAtTime(0.3, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.35);
                } else {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(220, ctx.currentTime);
                    osc.frequency.setValueAtTime(146.83, ctx.currentTime + 0.15);
                    gain.gain.setValueAtTime(0.3, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.3);
                }
            } catch (e) {
                console.warn('Audio chime skipped: ', e);
            }
        }

        // Live Clock
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour12: false });
            const clockEl = document.getElementById('kioskClock');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Scan Mode buttons toggle
        function setScanMode(mode) {
            currentScanMode = mode;
            ['auto', 'in', 'out'].forEach(m => {
                const btn = document.getElementById('btn-mode-' + m);
                if (m === mode) {
                    btn.className = 'px-3.5 py-1.5 rounded-xl font-bold bg-indigo-600 text-white shadow-sm transition';
                } else {
                    btn.className = 'px-3.5 py-1.5 rounded-xl font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 transition';
                }
            });
        }

        // Process Scanned QR Secret
        async function submitQrSecret(qrSecret) {
            if (!qrSecret || isProcessing) return;
            isProcessing = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch("{{ route('scanner.verify') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        qr_secret: qrSecret,
                        action: currentScanMode,
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    playChime(true);
                    showResultCard(data);
                    prependRecentScan(data);
                } else {
                    playChime(false);
                    showErrorResult(data.message || 'QR Verification failed.');
                }
            } catch (err) {
                playChime(false);
                showErrorResult('Connection error verifying attendance.');
            } finally {
                document.getElementById('manualSecretInput').value = '';
                // Cooldown so one card doesn't scan multiple times consecutively
                setTimeout(() => {
                    isProcessing = false;
                }, 3000);
            }
        }

        function showResultCard(data) {
            document.getElementById('standbyCard').classList.add('hidden');
            const card = document.getElementById('resultCard');
            card.classList.remove('hidden');

            document.getElementById('resultAvatar').src = data.user.avatar;
            document.getElementById('resultName').textContent = data.user.name;
            document.getElementById('resultPosition').textContent = data.user.position + ' • ' + data.user.department;

            const isTimeIn = data.type === 'in';
            const badge = document.getElementById('resultStatusBadge');
            badge.textContent = isTimeIn ? 'TIME IN RECORDED' : 'TIME OUT RECORDED';
            badge.className = isTimeIn
                ? 'inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1 bg-[#00ff00]/20 text-[#00ff00] border border-[#00ff00]/50 shadow-[0_0_8px_rgba(0,255,0,0.3)]'
                : 'inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1 bg-[rgb(0,187,255)]/20 text-[rgb(0,187,255)] border border-[rgb(0,187,255)]/50 shadow-[0_0_8px_rgba(0,187,255,0.3)]';

            document.getElementById('resultActionText').textContent = isTimeIn ? 'Checked In' : 'Checked Out';
            document.getElementById('resultTimeText').textContent = data.time;

            const msgBox = document.getElementById('resultMessage');
            msgBox.textContent = data.message;
            msgBox.className = 'mt-4 p-3 rounded-xl text-xs font-semibold bg-black/80 text-[#00ff00] border border-[#00ff00]/60 shadow-[0_0_10px_rgba(0,255,0,0.2)]';

            // Auto reset back to standby after 4 seconds
            setTimeout(() => {
                card.classList.add('hidden');
                document.getElementById('standbyCard').classList.remove('hidden');
            }, 4500);
        }

        function showErrorResult(message) {
            document.getElementById('standbyCard').classList.add('hidden');
            const card = document.getElementById('resultCard');
            card.classList.remove('hidden');

            document.getElementById('resultAvatar').src = 'https://ui-avatars.com/api/?name=Alert&background=ef4444&color=fff';
            document.getElementById('resultName').textContent = 'Attention Needed';
            document.getElementById('resultPosition').textContent = 'QR Attendance Alert';

            const badge = document.getElementById('resultStatusBadge');
            badge.textContent = 'VERIFICATION NOTICE';
            badge.className = 'inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider mb-1 bg-rose-500/20 text-rose-300 border border-rose-500/30';

            document.getElementById('resultActionText').textContent = 'Action Blocked';
            document.getElementById('resultTimeText').textContent = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

            const msgBox = document.getElementById('resultMessage');
            msgBox.textContent = message;
            msgBox.className = 'mt-4 p-3 rounded-xl text-xs font-medium bg-rose-950/70 text-rose-300 border border-rose-500/40';

            setTimeout(() => {
                card.classList.add('hidden');
                document.getElementById('standbyCard').classList.remove('hidden');
            }, 4500);
        }

        function prependRecentScan(data) {
            const list = document.getElementById('recentScansList');
            const item = document.createElement('div');
            item.className = 'p-2.5 rounded-xl bg-slate-950/70 border border-slate-800/80 flex items-center justify-between animate-fade-in';
            item.innerHTML = `
                <div class="flex items-center space-x-2.5">
                    <img src="${data.user.avatar}" class="w-7 h-7 rounded-full object-cover">
                    <div>
                        <div class="font-bold text-white text-xs">${data.user.name}</div>
                        <div class="text-[10px] text-slate-400">${data.user.department}</div>
                    </div>
                </div>
                <div class="text-right font-mono">
                    <div class="text-emerald-400 font-semibold">${data.type.toUpperCase()}: ${data.time}</div>
                    <div class="text-[10px] text-slate-500">${data.status}</div>
                </div>
            `;
            list.prepend(item);
        }

        // Initialize Camera
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Html5Qrcode) {
                html5QrCode = new window.Html5Qrcode("reader");

                window.Html5Qrcode.getCameras().then(cameras => {
                    const select = document.getElementById('cameraSelect');
                    select.innerHTML = '';
                    if (cameras && cameras.length) {
                        cameras.forEach((camera, index) => {
                            const option = document.createElement('option');
                            option.value = camera.id;
                            option.text = camera.label || `Camera ${index + 1}`;
                            select.appendChild(option);
                        });

                        const cameraId = cameras[0].id;
                        startScanner(cameraId);

                        select.addEventListener('change', () => {
                            html5QrCode.stop().then(() => {
                                startScanner(select.value);
                            });
                        });
                    } else {
                        document.getElementById('cameraStatus').textContent = 'No camera found. Use manual input.';
                    }
                }).catch(err => {
                    console.warn("Camera enumeration error:", err);
                    document.getElementById('cameraStatus').textContent = 'Camera permission required';
                });
            }
        });

        function startScanner(cameraId) {
            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.333334
                },
                (decodedText) => {
                    submitQrSecret(decodedText);
                },
                (errorMessage) => {
                    // Ignore scan loop framing failures
                }
            ).catch(err => {
                console.error("Unable to start scanning:", err);
                document.getElementById('cameraStatus').textContent = 'Camera error';
            });
        }
    </script>
</body>
</html>
