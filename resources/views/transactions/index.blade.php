@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="page-heading">
        <div>
            <h1>Riwayat Transaksi</h1>
            <p>Telusuri transaksi dan buka struk untuk detail pesanan.</p>
        </div>
        <div class="heading-actions">
            <button class="btn btn-outline" type="button" onclick="openFilterModal()">
                Filter
            </button>

            <a class="btn btn-outline" href="{{ route('reports.index') }}">
                <x-icon name="chart" /> Laporan
            </a>
        </div>
    </div>

    <section class="panel">
        <x-table>
            <thead>
                <tr>
                    <th>NO. INVOICE</th>
                    <th>TANGGAL</th>
                    <th>TOTAL</th>
                    <th>METODE</th>
                    <th>STATUS</th>
                    <th>KASIR</th>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody id="transaction-table">
                @forelse($transactions as $transaction)
                    <tr>
                        <td>
                            <span class="table-primary">
                                {{ $transaction->invoice }}
                            </span>
                        </td>
                        <td>
                            {{ $transaction->paid_at->translatedFormat('d M Y H:i') }}
                        </td>
                        <td>
                            <strong>
                                Rp {{ number_format($transaction->total, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge badge-neutral">
                                {{ $transaction->payment_method }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-success">
                                Selesai
                            </span>
                        </td>
                        <td>
                            {{ $transaction->cashier->name }}
                        </td>
                        <td>
                            <a class="table-action" href="{{ route('transactions.receipt', $transaction) }}"
                                aria-label="Lihat struk {{ $transaction->invoice }}">
                                <x-icon name="eye" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            Belum ada transaksi yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <div class="pagination-row">
            <span>
                Menampilkan
                {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }}
                dari {{ $transactions->total() }} transaksi
            </span>

            <div class="pagination-buttons">
                {{ $transactions->links() }}
            </div>
        </div>
    </section>


    {{-- Filter Modal --}}
    <div id="filter-modal" class="modal-overlay" aria-hidden="true">
        <div class="modal" role="dialog" aria-modal="true" aria-labelledby="filter-modal-title">
            <div class="modal-header">
                <div>
                    <h2 id="filter-modal-title">Filter Transaksi</h2>
                    <p>Pilih filter untuk menampilkan transaksi.</p>
                </div>
                <button type="button" class="modal-close" onclick="closeFilterModal()" aria-label="Tutup">
                    &times;
                </button>
            </div>

            <form method="get" action="{{ route('transactions.index') }}">
                <div class="modal-body">
                    <div class="filter-field">
                        <label for="transaction-search">Cari Invoice</label>
                        <div class="search-control">
                            <x-icon name="search" />

                            <input id="transaction-search" class="input-control" type="search" name="search"
                                value="{{ request('search') }}" placeholder="Cari invoice...">
                        </div>
                    </div>
                    <div class="filter-field">
                        <label for="method-filter">Metode Pembayaran</label>
                        <select id="method-filter" class="select-control" name="method">
                            <option value="">Semua metode</option>
                            @foreach (['Tunai', 'QRIS'] as $method)
                                <option value="{{ $method }}" @selected(request('method') === $method)>
                                    {{ $method }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-field">
                        <label for="transaction-status-filter">Status</label>
                        <select id="transaction-status-filter" class="select-control" name="status">
                            <option value="">Semua status</option>
                            <option value="completed" @selected(request('status') === 'completed')>
                                Selesai
                            </option>
                        </select>
                    </div>

                    <div class="filter-grid">
                        <div class="filter-field">
                            <label for="from-date">Dari Tanggal</label>
                            <input class="input-control" id="from-date" name="from" type="date"
                                value="{{ request('from') }}">
                        </div>
                        <div class="filter-field">
                            <label for="to-date">Sampai Tanggal</label>
                            <input class="input-control" id="to-date" name="to" type="date"
                                value="{{ request('to') }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a class="btn btn-quiet" href="{{ route('transactions.index') }}">
                        Reset
                    </a>
                    <div class="modal-footer-actions">
                        <button type="button" class="btn btn-outline" onclick="closeFilterModal()">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <style>
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(3px);
        }

        .modal-overlay.is-open {
            display: flex;
        }

        .modal {
            width: 100%;
            max-width: 520px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.2);
        }

        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 22px 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .modal-header p {
            margin: 5px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .modal-close {
            border: 0;
            background: transparent;
            color: #6b7280;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            padding: 0 4px;
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 18px;
            padding: 24px;
        }

        .filter-field {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .filter-field>label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 18px 24px;
            border-top: 1px solid #e5e7eb;
        }

        .modal-footer-actions {
            display: flex;
            gap: 8px;
        }

        body.modal-open {
            overflow: hidden;
        }

        @media (max-width: 600px) {
            .modal-overlay {
                align-items: flex-end;
                padding: 0;
            }

            .modal {
                max-width: none;
                max-height: 90vh;
                border-radius: 18px 18px 0 0;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .modal-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .modal-footer-actions {
                width: 100%;
            }

            .modal-footer-actions .btn {
                flex: 1;
            }
        }
    </style>


    <script>
        function openFilterModal() {
            const modal = document.getElementById('filter-modal');

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');

            document.body.classList.add('modal-open');

            setTimeout(() => {
                document.getElementById('transaction-search')?.focus();
            }, 100);
        }

        function closeFilterModal() {
            const modal = document.getElementById('filter-modal');

            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');

            document.body.classList.remove('modal-open');
        }

        document.getElementById('filter-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeFilterModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeFilterModal();
            }
        });
    </script>
@endsection
