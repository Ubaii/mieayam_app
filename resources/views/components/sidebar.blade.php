<style>
    /* Mobile / tablet: sidebar jadi drawer */
    @media (max-width: 1023.98px) {
        #app-sidebar { transform: translateX(-100%); transition: transform .2s ease-out; }
        body.sidebar-is-open #app-sidebar { transform: none; }
        body.sidebar-is-open [data-sidebar-backdrop] { display: block !important; }
    }
    /* Desktop: sidebar selalu tampil, tombol hamburger & tutup disembunyiin */
    @media (min-width: 1024px) {
        #app-sidebar { transform: none; }
        [data-sidebar-open], #sidebar-close { display: none !important; }
    }
</style>

<div class="fixed inset-0 bg-black/40 z-20 hidden" data-sidebar-backdrop></div>

<aside class="fixed top-0 left-0 bottom-0 w-[280px] max-w-[85vw] bg-[#2563eb] text-white flex flex-col z-30 overflow-y-auto p-6 pt-4" id="app-sidebar" aria-label="Navigasi utama">
    <div class="flex items-center justify-between mx-[6px] mb-[28px]">
        <a class="flex items-center text-white" href="{{ route(auth()->user()->isAdmin() ? 'dashboard' : 'cashier') }}">
            <span class="block text-[14px] font-bold text-white tracking-[.08em]">MIE AYAM WENGI'57</span>
        </a>
        {{-- Tombol tutup (mobile) --}}
        <button type="button" id="sidebar-close" class="lg:hidden inline-flex items-center justify-center w-[30px] h-[30px] rounded-lg bg-transparent border-0 text-white hover:bg-white/15" aria-label="Tutup menu">
            <svg class="w-[18px] h-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </div>

    <nav class="flex flex-col flex-1 gap-[4px]">
        <p class="text-[10px] font-bold text-white tracking-[.09em] mx-[10px] mb-[5px]">PLATFORM</p>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('dashboard') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[36px] hover:bg-white/15', 'bg-white/25 font-semibold' => request()->routeIs('dashboard')])>
            <x-icon name="dashboard" /> <span>Dashboard</span>
        </a>
        @endif
        <a href="{{ route('cashier') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[36px] hover:bg-white/15', 'bg-white/25 font-semibold' => request()->routeIs('cashier')])>
            <x-icon name="cashier" /> <span>Kasir</span>
        </a>

        @if(auth()->user()->isAdmin())
        <p class="text-[10px] font-bold text-white tracking-[.09em] mx-[10px] mb-[5px] mt-[17px]">MANAJEMEN</p>
        <a href="{{ route('categories.index') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[36px] hover:bg-white/15', 'bg-white/25 font-semibold' => request()->routeIs('categories.*')])>
            <x-icon name="category" /> <span>Kategori</span>
        </a>
        <a href="{{ route('menus.index') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[36px] hover:bg-white/15', 'bg-white/25 font-semibold' => request()->routeIs('menus.*')])>
            <x-icon name="coffee" /> <span>Menu</span>
        </a>

        <p class="text-[10px] font-bold text-white tracking-[.09em] mx-[10px] mb-[5px] mt-[17px]">LAPORAN</p>
        <a href="{{ route('transactions.index') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[36px] hover:bg-white/15', 'bg-white/25 font-semibold' => request()->routeIs('transactions.index')])>
            <x-icon name="receipt" /> <span>Transaksi</span>
        </a>
        <a href="{{ route('reports.index') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[36px] hover:bg-white/15', 'bg-white/25 font-semibold' => request()->routeIs('reports.*')])>
            <x-icon name="chart" /> <span>Laporan Penjualan</span>
        </a>
        @endif
    </nav>

    <div class="border-t border-white/20 mt-[18px] pt-[15px]">
        <div class="flex items-center gap-[10px] py-[6px] px-[8px] pb-[12px]">
            <span class="inline-flex items-center justify-center w-[34px] h-[34px] bg-white text-[#2563eb] text-[12px] font-bold rounded-full">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="flex flex-col gap-[3px] flex-1"><strong class="text-[12px] font-semibold text-white">{{ auth()->user()->name }}</strong><small class="text-[10px] text-white">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Kasir' }}</small></span>
            <x-icon name="chevron" class="w-[15px] h-[15px] text-white" />
        </div>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[34px] hover:bg-white/15 w-full text-left bg-transparent border-0"><x-icon name="logout" /><span>Keluar</span></button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sidebar = document.getElementById('app-sidebar');
        var backdrop = document.querySelector('[data-sidebar-backdrop]');
        var openBtns = document.querySelectorAll('[data-sidebar-open]');
        var closeBtn = document.getElementById('sidebar-close');
        if (!sidebar || !backdrop) return;

        function setOpen(open) {
            document.body.classList.toggle('sidebar-is-open', open);
            openBtns.forEach(function (b) {
                b.setAttribute('aria-expanded', open ? 'true' : 'false');
                var hamburger = b.querySelector('.hamburger');
                if (hamburger) hamburger.classList.toggle('active', open);
            });
            document.body.style.overflow = open ? 'hidden' : '';
        }

        openBtns.forEach(function (b) { b.setAttribute('aria-controls', 'app-sidebar'); b.addEventListener('click', function () { setOpen(true); }); });
        if (closeBtn) closeBtn.addEventListener('click', function () { setOpen(false); });
        backdrop.addEventListener('click', function () { setOpen(false); });
        sidebar.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { setOpen(false); });
        });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        window.matchMedia('(min-width: 1024px)').addEventListener('change', function (e) { if (e.matches) setOpen(false); });
    });
</script>