<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password — Corndog-Ku</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

<div class="min-h-screen relative text-gray-900 font-sans overflow-hidden bg-[#FEFDF2]">

    {{-- Background texture --}}
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('assets/img/login-logout_background.png') }}"
             alt="" class="w-full h-full object-cover" aria-hidden="true">
    </div>

    {{-- Back --}}
    <a href="{{ route('login') }}"
       class="absolute top-4 left-4 md:left-10 text-red-700 hover:text-red-900 inline-flex items-center gap-2 z-50 font-bold py-2 px-2 min-h-[44px]">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>

    {{-- Centred card --}}
    <div class="absolute inset-0 flex flex-col justify-center items-center z-30 px-4">
        <div class="max-w-md w-full bg-white p-10 rounded-2xl shadow-xl">

            <div class="text-center mb-8">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Corndog-Ku" class="h-10 w-auto mx-auto mb-4">
                <h1 class="text-3xl font-helvetica font-semibold text-gray-900">Reset Password</h1>
                <p id="step-subtitle" class="mt-2 text-sm text-gray-500">
                    Masukkan email kamu untuk menerima kode OTP via WhatsApp.
                </p>
            </div>

            {{-- Alert (success / error) --}}
            <div id="alert-box" class="hidden mb-5 text-sm rounded-lg px-4 py-3"></div>

            {{-- ───── STEP 1: Request OTP ───── --}}
            <form id="step-1" onsubmit="return false;">
                <div class="space-y-4">
                    <div>
                        <input type="email" id="email" name="email" placeholder="Email"
                               required autofocus autocomplete="email"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm outline-none
                                      focus:border-red-400 transition-colors">
                    </div>
                </div>

                <button type="submit" id="btn-send-otp"
                        class="w-full mt-6 bg-[#B82B21] text-white font-bold py-2.5 px-4 rounded-md
                               hover:bg-red-800 transition-colors tracking-widest text-sm
                               disabled:opacity-60 disabled:cursor-not-allowed">
                    KIRIM OTP VIA WA
                </button>
            </form>

            {{-- ───── STEP 2: Verify OTP ───── --}}
            <form id="step-2" class="hidden" onsubmit="return false;">
                <div class="space-y-4">
                    <div>
                        <input type="text" id="otp" name="otp" inputmode="numeric" maxlength="6"
                               placeholder="Kode OTP" autocomplete="one-time-code"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm outline-none
                                      tracking-[0.5em] text-center focus:border-red-400 transition-colors">
                    </div>
                </div>

                <button type="submit" id="btn-verify"
                        class="w-full mt-6 bg-[#B82B21] text-white font-bold py-2.5 px-4 rounded-md
                               hover:bg-red-800 transition-colors tracking-widest text-sm
                               disabled:opacity-60 disabled:cursor-not-allowed">
                    VERIFIKASI OTP
                </button>

                <div class="flex justify-between items-center mt-4 text-sm">
                    <button type="button" id="btn-change-number"
                            class="text-gray-600 hover:text-red-700 transition-colors">
                        &laquo; Ganti Email
                    </button>

                    <button type="button" id="btn-resend-otp"
                            class="text-red-700 font-semibold hover:underline
                                   disabled:opacity-60 disabled:cursor-not-allowed disabled:no-underline">
                        Kirim Ulang OTP
                    </button>
                </div>
            </form>

            {{-- ───── STEP 3: New Password ───── --}}
            <form id="step-3" class="hidden" onsubmit="return false;">
                <div class="space-y-4">
                    <div>
                        <input type="password" id="password" name="password" placeholder="Password Baru"
                               autocomplete="new-password"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm outline-none
                                      focus:border-red-400 transition-colors">
                    </div>
                    <div>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               placeholder="Konfirmasi Password" autocomplete="new-password"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm outline-none
                                      focus:border-red-400 transition-colors">
                    </div>
                </div>

                <button type="submit" id="btn-reset"
                        class="w-full mt-6 bg-[#B82B21] text-white font-bold py-2.5 px-4 rounded-md
                               hover:bg-red-800 transition-colors tracking-widest text-sm
                               disabled:opacity-60 disabled:cursor-not-allowed">
                    SIMPAN PASSWORD BARU
                </button>
            </form>

            <div class="text-center mt-8 text-sm text-gray-600">
                Ingat password kamu?
                <a href="{{ route('login') }}" class="text-red-700 font-bold hover:underline">Masuk</a>
            </div>
        </div>
    </div>

    {{-- Decorative corndog images — large, anchored to bottom corners --}}
    <div class="absolute bottom-0 left-0 z-20 hidden lg:block origin-bottom-left">
        <img src="{{ asset('assets/img/login-logout_corndog_01.png') }}"
             alt="" class="h-[400px] xl:h-[550px] 2xl:h-[650px] w-auto drop-shadow-2xl"
             aria-hidden="true">
    </div>
    <div class="absolute bottom-0 left-12 xl:left-20 z-10 hidden lg:block origin-bottom-left">
        <img src="{{ asset('assets/img/login-logout_corndog_02.png') }}"
             alt="" class="h-[300px] xl:h-[450px] 2xl:h-[500px] w-auto drop-shadow-xl opacity-90"
             aria-hidden="true">
    </div>
    <div class="absolute bottom-0 right-0 z-20 hidden lg:block origin-bottom-right">
        <img src="{{ asset('assets/img/login-logout_corndog_03.png') }}"
             alt="" class="h-[350px] xl:h-[500px] 2xl:h-[600px] w-auto drop-shadow-2xl"
             aria-hidden="true">
    </div>

</div>

<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const step1     = document.getElementById('step-1');
    const step2     = document.getElementById('step-2');
    const step3     = document.getElementById('step-3');
    const subtitle  = document.getElementById('step-subtitle');
    const alertBox  = document.getElementById('alert-box');
    const emailEl   = document.getElementById('email');
    const btnSend   = document.getElementById('btn-send-otp');
    const btnVerify = document.getElementById('btn-verify');
    const btnReset  = document.getElementById('btn-reset');
    const btnChange = document.getElementById('btn-change-number');
    const btnResend = document.getElementById('btn-resend-otp');

    function showAlert(message, type) {
        alertBox.textContent = message;
        alertBox.className = 'mb-5 text-sm rounded-lg px-4 py-3 border ' + (type === 'success'
            ? 'text-green-700 bg-green-50 border-green-200'
            : 'text-red-700 bg-red-50 border-red-200');
        alertBox.classList.remove('hidden');
    }

    function clearAlert() {
        alertBox.classList.add('hidden');
    }

    function goToStep1() {
        step2.classList.add('hidden');
        step3.classList.add('hidden');
        step1.classList.remove('hidden');
        subtitle.textContent = 'Masukkan email kamu untuk menerima kode OTP via WhatsApp.';
        emailEl.focus();
    }

    function goToStep2() {
        step1.classList.add('hidden');
        step3.classList.add('hidden');
        step2.classList.remove('hidden');
        subtitle.textContent = 'Masukkan kode OTP yang dikirim ke WhatsApp kamu.';
        document.getElementById('otp').focus();
    }

    function goToStep3() {
        step1.classList.add('hidden');
        step2.classList.add('hidden');
        step3.classList.remove('hidden');
        subtitle.textContent = 'Buat password baru untuk akun kamu.';
        document.getElementById('password').focus();
    }

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify(payload),
        });
        let data = {};
        try { data = await res.json(); } catch (e) {}
        return { ok: res.ok, data };
    }

    // Shared OTP-send routine — used by both Step 1 and "Kirim Ulang OTP".
    // `advance` controls whether a success moves the UI forward to Step 2.
    async function sendOtp(triggerBtn, busyLabel, idleLabel, advance) {
        clearAlert();
        const email = emailEl.value.trim();
        if (!email) { showAlert('Masukkan email kamu.', 'error'); return; }

        triggerBtn.disabled = true;
        triggerBtn.textContent = busyLabel;

        const { ok, data } = await postJson('{{ route('password.wa.send') }}', { email });

        triggerBtn.disabled = false;
        triggerBtn.textContent = idleLabel;

        if (ok && data.success) {
            showAlert(data.message || 'Kode OTP telah dikirim.', 'success');
            if (advance) goToStep2();
        } else {
            showAlert(data.message || 'Terjadi kesalahan. Coba lagi.', 'error');
        }
    }

    // ── Step 1: send OTP ──
    step1.addEventListener('submit', function () {
        sendOtp(btnSend, 'MENGIRIM…', 'KIRIM OTP VIA WA', true);
    });

    // ── Step 2: verify OTP only ──
    step2.addEventListener('submit', async function () {
        clearAlert();
        const otp = document.getElementById('otp').value.trim();
        if (otp.length !== 6) { showAlert('Kode OTP harus 6 digit.', 'error'); return; }

        btnVerify.disabled = true;
        btnVerify.textContent = 'MEMVERIFIKASI…';

        const { ok, data } = await postJson('{{ route('password.wa.verify') }}', {
            otp: otp,
        });

        btnVerify.disabled = false;
        btnVerify.textContent = 'VERIFIKASI OTP';

        if (ok && data.success) {
            showAlert(data.message || 'OTP terverifikasi.', 'success');
            goToStep3();
        } else {
            showAlert(data.message || 'Terjadi kesalahan. Coba lagi.', 'error');
        }
    });

    // ── Step 3: set new password ──
    step3.addEventListener('submit', async function () {
        clearAlert();
        const password = document.getElementById('password').value;
        const confirm  = document.getElementById('password_confirmation').value;

        if (password.length < 8)  { showAlert('Password baru minimal 8 karakter.', 'error'); return; }
        if (password !== confirm) { showAlert('Konfirmasi password tidak cocok.', 'error'); return; }

        btnReset.disabled = true;
        btnReset.textContent = 'MENYIMPAN…';

        const { ok, data } = await postJson('{{ route('password.wa.reset') }}', {
            password: password,
            password_confirmation: confirm,
        });

        if (ok && data.success) {
            showAlert(data.message || 'Password berhasil diperbarui.', 'success');
            setTimeout(() => { window.location.href = data.redirect || '{{ route('login') }}'; }, 1500);
        } else {
            btnReset.disabled = false;
            btnReset.textContent = 'SIMPAN PASSWORD BARU';
            showAlert(data.message || 'Terjadi kesalahan. Coba lagi.', 'error');
        }
    });

    // ── "Ganti Nomor" — return to Step 1 to enter a different number ──
    btnChange.addEventListener('click', function () {
        clearAlert();
        document.getElementById('otp').value = '';
        goToStep1();
    });

    // ── "Kirim Ulang OTP" — resend to the same number, staying on Step 2 ──
    btnResend.addEventListener('click', function () {
        sendOtp(btnResend, 'Mengirim…', 'Kirim Ulang OTP', false);
    });
})();
</script>

</body>
</html>
