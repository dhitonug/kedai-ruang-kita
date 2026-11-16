@extends('layouts.admin')

@section('title', 'Riwayat pesanan · Kedai Ruang Kita')
@section('crumb', 'Riwayat pesanan')

@section('content')
    <div class="page-head">
        <div>
            <h1>Riwayat pesanan</h1>
            <p>Pesanan selesai dan dibatalkan tersimpan di sini.</p>
        </div>
        <a class="btn" href="{{ route('admin.orders') }}">← Kembali ke antrean</a>
    </div>

    <form class="history-filter" method="GET" action="{{ route('admin.orders.history') }}">
        <label for="historyStatus">Tampilkan</label>
        <select class="select" id="historyStatus" name="status" onchange="this.form.submit()">
            <option value="" @selected(!$status)>Selesai dan dibatalkan</option>
            <option value="selesai" @selected($status === 'selesai')>Selesai</option>
            <option value="dibatalkan" @selected($status === 'dibatalkan')>Dibatalkan</option>
        </select>
        <span class="muted">{{ $orders->total() }} pesanan</span>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>NO. PESANAN</th>
                    <th>ASAL / MEJA</th>
                    <th>RINCIAN</th>
                    <th>CATATAN</th>
                    <th>TOTAL</th>
                    <th>STATUS</th>
                    <th>WAKTU</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><b>{{ $order->code }}</b></td>
                        <td>{{ $order->table_number ? 'Meja ' . $order->table_number : $order->source }}</td>
                        <td>
                            @foreach ($order->items as $item)
                                <div>{{ $item->quantity }}× {{ $item->product_name }}</div>
                            @endforeach
                        </td>
                        <td class="muted">{{ $order->notes ?: '—' }}</td>
                        <td><b>Rp {{ number_format($order->total, 0, ',', '.') }}</b></td>
                        <td><span class="badge {{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('d/m · H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="muted">Belum ada pesanan di riwayat.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $orders->withQueryString()->links() }}</div>
    </div>
@endsection
