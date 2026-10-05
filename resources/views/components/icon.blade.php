@props(['name'])
<svg {{ $attributes->merge(['class' => 'icon', 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '1.7', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('arrow-left')
            <path d="m14.5 5-7 7 7 7M8 12h12"/>
            @break
        @case('dashboard')
            <rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="4" rx="1.5"/><rect x="13.5" y="10.5" width="7" height="10" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/>
            @break
        @case('cashier')
            <path d="M4 5h2l2.1 10.1a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 1.9-1.4L21 9H7"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>
            @break
        @case('category')
            <rect x="4" y="4" width="6" height="6" rx="1.5"/><rect x="14" y="4" width="6" height="6" rx="1.5"/><rect x="4" y="14" width="6" height="6" rx="1.5"/><rect x="14" y="14" width="6" height="6" rx="1.5"/>
            @break
        @case('coffee')
            <path d="M5 8h12v7a5 5 0 0 1-5 5h-2a5 5 0 0 1-5-5V8Z"/><path d="M17 10h1.5a2.5 2.5 0 0 1 0 5H17M8 4v2M12 3v3M16 4v2"/>
            @break
        @case('table')
            <rect x="3.5" y="4.5" width="17" height="11" rx="2"/><path d="M7 19.5v-4M17 19.5v-4M8 8.5h8M8 11.5h5"/>
            @break
        @case('eye')
            <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/>
            @break
        @case('eye-off')
            <path d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.3A10.7 10.7 0 0 1 12 5c6.1 0 9.5 7 9.5 7a15 15 0 0 1-3 3.7M6.2 6.2C3.9 7.8 2.5 12 2.5 12s3.4 7 9.5 7a10.7 10.7 0 0 0 2.1-.3"/>
            @break
        @case('receipt')
            <path d="M6 3.5h12v17l-3-1.8-3 1.8-3-1.8-3 1.8v-17Z"/><path d="M9 8h6M9 12h6M9 16h3"/>
            @break
        @case('chart')
            <path d="M4 19.5h16"/><rect x="5" y="11" width="3.5" height="7" rx="1"/><rect x="10.3" y="7" width="3.5" height="11" rx="1"/><rect x="15.5" y="4" width="3.5" height="14" rx="1"/>
            @break
        @case('search')
            <circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/>
            @break
        @case('menu')
            <path d="M4 6h16M4 12h16M4 18h16"/>
            @break
        @case('bell')
            <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9ZM10 21h4"/>
            @break
        @case('chevron')
            <path d="m8 10 4 4 4-4"/>
            @break
        @case('logout')
            <path d="M10 5H5v14h5M14 8l4 4-4 4M18 12H9"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('minus')
            <path d="M5 12h14"/>
            @break
        @case('edit')
            <path d="m15 5 4 4M4 20l4-.8L19 8a2.8 2.8 0 0 0-4-4L4 15v5Z"/>
            @break
        @case('trash')
            <path d="M4 7h16M10 11v6M14 11v6M6 7l1 14h10l1-14M9 7V4h6v3"/>
            @break
        @case('calendar')
            <rect x="3.5" y="5" width="17" height="16" rx="2"/><path d="M7.5 3v4M16.5 3v4M3.5 10h17"/>
            @break
        @case('wallet')
            <rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 8h18M16 14h2"/>
            @break
        @case('bag')
            <path d="M5 8h14l1 12H4L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
            @break
        @case('download')
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
            @break
        @case('close')
            <path d="m6 6 12 12M18 6 6 18"/>
            @break
    @endswitch
</svg>
