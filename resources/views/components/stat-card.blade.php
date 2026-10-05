@props(['label', 'value', 'icon' => 'chart', 'tone' => 'teal', 'note' => null])
<article class="stat-card">
    <span @class(['stat-icon', 'tone-blue' => $tone === 'blue', 'tone-green' => $tone === 'green', 'tone-amber' => $tone === 'amber', 'tone-violet' => $tone === 'violet', 'tone-teal' => $tone === 'teal'])>
        <x-icon :name="$icon" />
    </span>
    <div class="stat-copy">
        <p>{{ $label }}</p>
        <strong>{{ $value }}</strong>
        @if($note)<small>{{ $note }}</small>@endif
    </div>
</article>
