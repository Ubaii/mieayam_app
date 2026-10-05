@props(['fallback', 'label' => 'Kembali'])

<a href="{{ $fallback }}" data-history-back {{ $attributes->merge(['class' => 'back-button']) }}>
    <x-icon name="arrow-left" />
    <span>{{ $label }}</span>
</a>
