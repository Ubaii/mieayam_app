<style>
    /* Mobile / tablet: sidebar jadi drawer */
    @media (max-width: 1023.98px) {
        #app-sidebar { transform: translateX(-100%); transition: transform .2s ease-out; }
        body.sidebar-is-open #app-sidebar { transform: none; }
        body.sidebar-is-open [data-sidebar-backdrop] { display: block !important; }
    }
    /* Desktop: sidebar selalu tampil, tombol hamburger disembunyiin */
    @media (min-width: 1024px) {
        #app-sidebar { transform: none; }
        [data-sidebar-open] { display: none !important; }
    }
</style>

<div class="fixed inset-0 bg-black/40 z-20 hidden" data-sidebar-backdrop></div>

<aside class="fixed top-0 left-0 bottom-0 w-[280px] max-w-[85vw] bg-[#1e40af] text-white flex flex-col z-30 overflow-y-auto p-6 pt-4 shadow-2xl" id="app-sidebar" aria-label="Navigasi utama">
    <div class="mb-[28px]">
        <a class="flex items-center text-white" href="{{ route(auth()->user()->isAdmin() ? 'dashboard' : 'cashier') }}">
            <span class="block text-[14px] font-bold text-white tracking-[.08em]">MIE AYAM WENGI'57</span>
        </a>
    </div>

    <nav class="flex flex-col flex-1 gap-[4px]">
        <p class="text-[10px] font-bold text-white/60 tracking-[.09em] mx-[10px] mb-[5px]">PLATFORM</p>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('dashboard') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[38px] transition-all duration-150', 'bg-blue-500/40 font-semibold shadow-lg' => request()->routeIs('dashboard'), 'hover:bg-white/10' => !request()->routeIs('dashboard')])>
            <x-icon name="dashboard" /> <span>Dashboard</span>
        </a>
        @endif
        <a href="{{ route('cashier') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[38px] transition-all duration-150', 'bg-blue-500/40 font-semibold shadow-lg' => request()->routeIs('cashier'), 'hover:bg-white/10' => !request()->routeIs('cashier')])>
            <x-icon name="cashier" /> <span>Kasir</span>
        </a>

        @if(auth()->user()->isAdmin())
        <p class="text-[10px] font-bold text-white/60 tracking-[.09em] mx-[10px] mb-[5px] mt-[17px]">MANAJEMEN</p>
        <a href="{{ route('categories.index') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[38px] transition-all duration-150', 'bg-blue-500/40 font-semibold shadow-lg' => request()->routeIs('categories.*'), 'hover:bg-white/10' => !request()->routeIs('categories.*')])>
            <x-icon name="category" /> <span>Kategori</span>
        </a>
        <a href="{{ route('menus.index') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[38px] transition-all duration-150', 'bg-blue-500/40 font-semibold shadow-lg' => request()->routeIs('menus.*'), 'hover:bg-white/10' => !request()->routeIs('menus.*')])>
            <x-icon name="coffee" /> <span>Menu</span>
        </a>

        <p class="text-[10px] font-bold text-white/60 tracking-[.09em] mx-[10px] mb-[5px] mt-[17px]">LAPORAN</p>
        <a href="{{ route('transactions.index') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[38px] transition-all duration-150', 'bg-blue-500/40 font-semibold shadow-lg' => request()->routeIs('transactions.index'), 'hover:bg-white/10' => !request()->routeIs('transactions.index')])>
            <x-icon name="receipt" /> <span>Transaksi</span>
        </a>
        <a href="{{ route('reports.index') }}" @class(['flex items-center gap-[11px] text-[13px] text-white py-0 px-[11px] rounded-lg min-h-[38px] transition-all duration-150', 'bg-blue-500/40 font-semibold shadow-lg' => request()->routeIs('reports.*'), 'hover:bg-white/10' => !request()->routeIs('reports.*')])>
            <x-icon name="chart" /> <span>Laporan Penjualan</span>
        </a>
        @endif
    </nav>

    <div class="border-t border-white/20 mt-[18px] pt-[15px]">
        <div class="flex items-center gap-[10px] py-[8px] px-[8px] pb-[12px] rounded-lg hover:bg-white/10 transition-colors duration-150">
            <span class="inline-flex items-center justify-center w-[36px] h-[36px] bg-blue-500 text-white text-[13px] font-bold rounded-full shadow-md">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            <span class="flex flex-col gap-[2px] flex-1"><strong class="text-[12px] font-semibold text-white">{{ auth()->user()->name }}</strong><small class="text-[10px] text-white/70">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Kasir' }}</small></span>
        </div>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="flex items-center gap-[11px] text-[13px] text-white/80 py-0 px-[11px] rounded-lg min-h-[36px] hover:bg-white/10 hover:text-white transition-all duration-150 w-full text-left bg-transparent border-0"><x-icon name="logout" /><span>Keluar</span></button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sidebar = document.getElementById('app-sidebar');
        var backdrop = document.querySelector('[data-sidebar-backdrop]');
        var openBtns = document.querySelectorAll('[data-sidebar-open]');
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

        openBtns.forEach(function (b) { b.setAttribute('aria-controls', 'app-sidebar'); b.addEventListener('click', function () { setOpen(!document.body.classList.contains('sidebar-is-open')); }); });
        backdrop.addEventListener('click', function () { setOpen(false); });
        sidebar.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { setOpen(false); });
        });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
        window.matchMedia('(min-width: 1024px)').addEventListener('change', function (e) { if (e.matches) setOpen(false); });
    });
</script>