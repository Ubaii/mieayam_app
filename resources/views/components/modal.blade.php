@props(['id', 'title'])
<div class="modal-backdrop" id="{{ $id }}" aria-hidden="true" data-modal>
    <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <header class="modal-header">
            <h2 id="{{ $id }}-title">{{ $title }}</h2>
            <button type="button" class="icon-button" aria-label="Tutup" data-modal-close><x-icon name="close" /></button>
        </header>
        <div class="modal-body">{{ $slot }}</div>
    </section>
</div>
