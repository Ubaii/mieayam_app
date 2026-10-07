<header
    class="fixed top-0 left-0 right-0 lg:left-[268px] z-10 flex items-center justify-between h-[56px] sm:h-[56px] lg:h-[64px] px-4 sm:px-5 lg:px-8 lg:py-2 bg-white text-[#1f2937] shadow-sm rounded-none mx-0 lg:mt-0">

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

    {{-- MOBILE --}}
    <div class="flex items-center lg:hidden">
        <div class="flex flex-col justify-center">
            <p class="text-[13px] sm:text-[14px] font-bold tracking-[.06em] text-[#1f2937]">
                MIE AYAM WENGI'57
            </p>

            <p class="text-[10px] sm:text-[11px] text-gray-500">
                {{ now()->translatedFormat('D, d M Y') }}
            </p>
        </div>
    </div>

    {{-- HAMBURGER --}}
    <button type="button"
        class="lg:hidden shrink-0 inline-flex items-center justify-center w-[40px] h-[40px] rounded-lg bg-transparent border-0 text-[#374151] hover:bg-gray-100 active:bg-gray-200"
        aria-label="Toggle navigasi"
        aria-expanded="false"
        data-sidebar-open>

        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </button>

    {{-- DESKTOP --}}
    <div class="hidden lg:block fixed top-0 mr-2 right-0 z-[60]">
        <div class="flex flex-col items-end text-right pr-6 pt-4">
            <p class="text-[14px] font-bold tracking-[.04em] text-[#1f2937] whitespace-nowrap">WARUNG MIE AYAM WENGI'57 <span class="ml-[6px]"> · {{ auth()->user()->isAdmin() ? 'Administrator' : 'Kasir' }} </span> </p>
            <p class="text-[12px] mt-1 text-gray-500 whitespace-nowrap"> {{ now()->translatedFormat('l, d F Y') }} </p>
        </div>
    </div>

</header>