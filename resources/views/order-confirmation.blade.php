@extends('layouts.store')

@section('title', 'Pesanan diterima · Kedai Ruang Kita')

@section('content')
    <main class="confirmation" aria-labelledby="confirmation-title">
        <div class="confirm-icon" aria-hidden="true">✓</div>
        <p class="section-eyebrow mb-2">Pesanan diterima</p>
        <h1 class="section-title" id="confirmation-title">Terima kasih.</h1>
        <p class="section-description mx-auto mt-2">Pesananmu sudah masuk ke kasir dan segera disiapkan.</p>

        <div class="order-reference mt-4">
            <span>Nomor pesanan</span>
            <strong>{{ $order->code }}</strong>
            @if ($order->table_number)
                <small>Meja {{ $order->table_number }}</small>
            @endif
        </div>

        <div class="confirm-items">
            @foreach ($order->items as $item)
                <div class="cart-row">
                    <span>{{ $item->quantity }}× {{ $item->product_name }}</span>
                    <strong>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</strong>
                </div>
            @endforeach
            <div class="checkout-total">
                <span>Total</span>
                <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
        </div>

        <p class="confirmation-note">Pembayaran dilakukan di kasir.</p>
        <a class="btn-rk-primary confirmation-link" href="{{ $order->table_number ? route('table.menu', $order->table_number) : route('home') }}">
            Kembali ke menu
        </a>
    </main>
@endsection
