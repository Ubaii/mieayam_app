<header class="sticky top-0 z-20 flex items-center justify-between h-[56px] sm:h-[56px] lg:h-[64px] px-4 sm:px-5 lg:px-8 py-2 bg-white lg:bg-[#2563eb] text-[#1f2937] lg:text-white shadow-sm lg:shadow-none rounded-2xl lg:rounded-none mx-3 sm:mx-4 lg:mx-0">
    <style>
        .hamburger {
            width: 22px;
            height: 16px;
            position: relative;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .hamburger span {
            display: block;
            width: 100%;
            height: 2px;
            background: #374151;
            border-radius: 2px;
            transition: all 0.3s ease;
            transform-origin: center;
        }
        .lg .hamburger span {
            background: white;
        }
        .hamburger.active span:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }
        .hamburger.active span:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }
        .hamburger.active span:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }
    </style>

    {{-- Mobile: Logo & Tanggal --}}
    <div class="flex flex-col justify-center lg:hidden">
        <p class="text-[13px] sm:text-[14px] font-bold tracking-[.06em] text-[#1f2937]">
            MIE AYAM WENGI'57
        </p>
        <p class="text-[10px] sm:text-[11px] text-gray-500">
            {{ now()->translatedFormat('D, d M Y') }}
        </p>
    </div>

    {{-- Desktop: Logo & Tanggal inline --}}
    <div class="hidden lg:flex items-center gap-[10px]">
        <x-back-button class="shrink-0 text-white" :fallback="route(auth()->user()->isAdmin() ? 'dashboard' : 'cashier')" />
        <div class="leading-tight">
            <p class="text-[13px] lg:text-[14px] font-bold tracking-[.06em] text-white">
                MIE AYAM WENGI'57<span class="ml-[6px]">· {{ auth()->user()->isAdmin() ? 'Administrator' : 'Kasir' }}</span>
            </p>
            <p class="text-[11px] lg:text-[12px] text-white/70">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
    </div>

    {{-- Mobile: Hamburger di kanan --}}
    <button type="button" class="lg:hidden shrink-0 inline-flex items-center justify-center w-[40px] h-[40px] rounded-lg bg-transparent border-0 text-[#374151] hover:bg-gray-100 active:bg-gray-200" aria-label="Buka navigasi" aria-expanded="false" data-sidebar-open>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </button>

    {{-- Desktop: Status Toko --}}
    <div class="hidden lg:flex items-center shrink-0">
        <span class="inline-flex items-center gap-[7px] text-[12px] font-medium text-white px-[9px] py-[5px] rounded-full bg-white/15">
            <span class="w-[8px] h-[8px] rounded-full bg-green-400"></span>
            Toko buka
        </span>
    </div>
</header>