<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Corndog-Ku')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased relative overflow-x-hidden" style="background-color: #FFFBEE; min-height: 100vh;">

{{-- Guest layout for public pages (with the floating chatbot widget). --}}

    {{-- Red blob — top-right decorative --}}
    <div class="absolute top-0 right-0 pointer-events-none select-none hidden lg:block" style="z-index:0; overflow:hidden; width:160px; height:160px;">
        <div style="position:absolute; top:-40px; right:-40px; width:160px; height:160px; background:var(--color-primary); border-radius:50%;"></div>
        <div style="position:absolute; top:80px; right:-10px; width:70px; height:50px; background:var(--color-primary); border-radius:0 0 40px 40px; transform:rotate(-20deg);"></div>
    </div>

    {{-- Amber accent — bottom-left --}}
    <div class="absolute bottom-0 left-0 pointer-events-none select-none hidden lg:block"
         style="z-index:0; width:200px; height:160px; background:var(--color-accent); border-radius:0 60% 0 0; opacity:0.7;"></div>

    {{-- Decorative corndog images — left side (desktop only) --}}
    <div class="absolute hidden lg:flex flex-col pointer-events-none select-none"
         style="z-index:1; left:-0.5rem; top:18%; gap:0;">
        <img src="{{ asset('assets/img/CA_CHEETOS.png') }}" alt=""
             style="width:140px; transform:rotate(8deg); filter:drop-shadow(2px 4px 8px rgba(0,0,0,0.15));">
        <img src="{{ asset('assets/img/CA_ORIGINAL.png') }}" alt=""
             style="width:110px; transform:rotate(8deg); margin-top:-2.5rem; margin-left:3rem; opacity:0.85; filter:drop-shadow(2px 4px 8px rgba(0,0,0,0.12));">
    </div>

    {{-- Kembali link --}}
    <a href="@yield('kembali_href', url('/'))"
       class="absolute top-6 left-6 flex items-center gap-1 text-sm font-semibold z-10 hover:opacity-80 transition-opacity hidden sm:flex"
       style="color:#B20000;">
        ← Kembali
    </a>

    {{-- Page content — centred --}}
    <div class="relative flex min-h-screen items-center justify-center px-4 py-20 sm:py-16" style="z-index:2;">
        <div class="w-full max-w-sm">
            @yield('content')
        </div>
    </div>


    {{-- Chatbot widget. --}}

    {{-- Floating trigger button — #FFBE54 pill, 15px radius, same-color badge with white ring (Figma 347:4278) --}}
    <button id="chatbot-trigger"
            aria-label="Buka chat asisten"
            class="fixed bottom-6 right-6 z-[9999] flex items-center gap-2 px-5 py-3 shadow-lg transition-transform hover:scale-105 active:scale-95"
            style="background-color: #FFBE54; border-radius: 15px;">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white flex-shrink-0" fill="currentColor"
             viewBox="0 0 24 24">
            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
        </svg>
        <span class="text-white text-base font-semibold tracking-tight">Chat</span>
        {{-- Badge: same yellow bg + white border ring, matches Figma 347:4290 --}}
        <span class="absolute -top-2.5 -right-2.5 w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center text-white border-2 border-white"
              style="background-color: #FFBE54;">1</span>
    </button>

    {{-- Chat window --}}
    <div id="chatbot-window"
         class="hidden fixed bottom-20 right-4 md:bottom-24 md:right-6 z-50 flex flex-col rounded-2xl overflow-hidden shadow-2xl
                w-[calc(100vw-2rem)] max-w-[340px] md:w-96 h-[70vh] max-h-[480px] md:h-[480px]"
         style="border: 1px solid #e5e7eb;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 flex-shrink-0"
             style="background-color: #8B0000;">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm leading-none">Corndog-Ku Assistant</p>
                    <p class="text-white/70 text-[10px] mt-0.5">Online · Biasanya membalas segera</p>
                </div>
            </div>
            <button id="chatbot-close"
                    class="text-white/80 hover:text-white transition-colors text-sm font-medium">
                × Tutup
            </button>
        </div>

        {{-- Messages area --}}
        <div id="chatbot-messages"
             class="flex-1 overflow-y-auto px-4 py-3 space-y-3"
             style="background-color: #FAFAFA;">

            {{-- Greeting bubble --}}
            <div class="flex items-end gap-2">
                <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center"
                     style="background-color: #8B0000;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>
                <div class="max-w-[75%] px-3 py-2 rounded-2xl rounded-bl-sm text-sm"
                     style="background-color: #F0F0F0; color: #1a1a1a;">
                    Halo! 👋 Aku asisten Corndog-Ku. Ada yang bisa aku bantu?
                </div>
            </div>
        </div>

        {{-- Input area --}}
        <div class="flex items-center gap-2 px-3 py-3 flex-shrink-0 bg-white border-t border-gray-100">
            <input id="chatbot-input"
                   type="text"
                   placeholder="Ketik pesan..."
                   autocomplete="off"
                   class="flex-1 px-4 py-2 text-sm rounded-full border border-gray-200 outline-none focus:border-red-300 focus:ring-2 focus:ring-red-100 transition-all"
                   style="background-color: #F9F9F9;">
            <button id="chatbot-send"
                    class="w-9 h-9 flex-shrink-0 rounded-full flex items-center justify-center transition-colors hover:opacity-80"
                    style="background-color: #8B0000;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Chatbot behaviour. --}}
    <script>
    (function () {
        const trigger   = document.getElementById('chatbot-trigger');
        const window_   = document.getElementById('chatbot-window');
        const closeBtn  = document.getElementById('chatbot-close');
        const messages  = document.getElementById('chatbot-messages');
        const input     = document.getElementById('chatbot-input');
        const sendBtn   = document.getElementById('chatbot-send');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const sendUrl   = '{{ route("chatbot.send") }}';

        let isSending = false;

        trigger.addEventListener('click', () => {
            window_.classList.toggle('hidden');
            if (!window_.classList.contains('hidden')) {
                input.focus();
                scrollToBottom();
            }
        });

        closeBtn.addEventListener('click', () => window_.classList.add('hidden'));

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
        });
        sendBtn.addEventListener('click', sendMessage);

        function sendMessage() {
            const text = input.value.trim();
            if (!text || isSending) return;

            isSending = true;
            input.value = '';
            sendBtn.disabled = true;

            appendBubble(text, 'user');

            const typingId = appendTyping();

            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text }),
            })
            .then(r => r.json())
            .then(data => {
                removeTyping(typingId);
                appendBubble(data.reply || 'Maaf, terjadi kesalahan.', 'bot');
            })
            .catch(() => {
                removeTyping(typingId);
                appendBubble('Maaf, koneksi bermasalah. Coba lagi ya!', 'bot');
            })
            .finally(() => {
                isSending = false;
                sendBtn.disabled = false;
                input.focus();
            });
        }

        function appendBubble(text, role) {
            const isUser = role === 'user';
            const wrapper = document.createElement('div');
            wrapper.className = 'flex items-end gap-2' + (isUser ? ' justify-end' : '');

            if (!isUser) {
                wrapper.innerHTML = `
                    <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center" style="background-color:#8B0000;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>`;
            }

            const bubble = document.createElement('div');
            bubble.className = 'max-w-[75%] px-3 py-2 rounded-2xl text-sm whitespace-pre-wrap';
            if (isUser) {
                bubble.style.cssText = 'background-color:#8B0000; color:#fff; border-radius:16px 16px 4px 16px;';
            } else {
                bubble.style.cssText = 'background-color:#F0F0F0; color:#1a1a1a; border-radius:16px 16px 16px 4px;';
            }
            bubble.textContent = text;
            wrapper.appendChild(bubble);

            messages.appendChild(wrapper);
            scrollToBottom();
        }

        function appendTyping() {
            const id = 'typing-' + Date.now();
            const wrapper = document.createElement('div');
            wrapper.id = id;
            wrapper.className = 'flex items-end gap-2';
            wrapper.innerHTML = `
                <div class="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center" style="background-color:#8B0000;">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                    </svg>
                </div>
                <div class="px-4 py-2.5 rounded-2xl" style="background-color:#F0F0F0; border-radius:16px 16px 16px 4px;">
                    <span class="flex gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:0ms"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:150ms"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-bounce" style="animation-delay:300ms"></span>
                    </span>
                </div>`;
            messages.appendChild(wrapper);
            scrollToBottom();
            return id;
        }

        function removeTyping(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        function scrollToBottom() {
            messages.scrollTop = messages.scrollHeight;
        }
    })();
    </script>

</body>
</html>
