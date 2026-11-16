@extends('layouts.admin')

@section('title', 'Antrean pesanan · Kedai Ruang Kita')
@section('crumb', 'Antrean pesanan')

@section('content')
    <div class="page-head order-board-head">
        <div>
            <h1>Antrean dapur</h1>
            <p>Pesanan tertua tampil lebih dulu. Pindahkan tiap pesanan dengan satu tombol.</p>
        </div>
        <div class="order-board-actions">
            <span class="queue-total">{{ $activeCount }} pesanan aktif</span>
            <a class="btn light" href="{{ route('admin.orders.history') }}">Riwayat pesanan</a>
            <button class="btn" type="button" onclick="window.location.reload()">Perbarui antrean</button>
        </div>
    </div>

    @if (session('success'))
        <div class="success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="success order-error" role="alert">{{ session('error') }}</div>
    @endif

    <p class="queue-refresh-note">Antrean diperbarui otomatis setiap 15 detik.</p>

    <div class="order-queues">
        @foreach ([['status' => 'baru', 'title' => 'Pesanan baru', 'subtitle' => 'Perlu mulai dikerjakan', 'orders' => $baru], ['status' => 'diproses', 'title' => 'Sedang dibuat', 'subtitle' => 'Pesanan di dapur', 'orders' => $diproses], ['status' => 'siap', 'title' => 'Siap disajikan', 'subtitle' => 'Menunggu diantar atau diambil', 'orders' => $siap]] as $queue)
            <section class="order-queue" aria-labelledby="queue-{{ $queue['status'] }}">
                <header class="queue-header">
                    <div>
                        <h2 id="queue-{{ $queue['status'] }}">{{ $queue['title'] }}</h2>
                        <p>{{ $queue['subtitle'] }}</p>
                    </div>
                    <span class="queue-count" aria-label="{{ $queue['orders']->count() }} pesanan">{{ $queue['orders']->count() }}</span>
                </header>

                <div class="queue-cards">
                    @forelse ($queue['orders'] as $order)
                        @php($ageMinutes = (int) $order->created_at->diffInMinutes(now()))
                        <article class="order-card">
                            <div class="order-card-top">
                                <div>
                                    <span class="order-code">{{ $order->code }}</span>
                                    <span class="order-source">{{ $order->table_number ? 'Meja ' . $order->table_number : $order->source }}</span>
                                </div>
                                <time class="order-age {{ $ageMinutes >= 10 ? 'order-age-late' : '' }}" datetime="{{ $order->created_at->toIso8601String() }}">
                                    {{ $order->created_at->format('H:i') }}
                                    @if ($ageMinutes === 0)
                                        <span>baru</span>
                                    @else
                                        <span>{{ $ageMinutes }} mnt lalu</span>
                                    @endif
                                </time>
                            </div>

                            @if ($order->customer_name)
                                <p class="order-customer">{{ $order->customer_name }}</p>
                            @endif

                            <ul class="order-items">
                                @foreach ($order->items as $item)
                                    <li><strong>{{ $item->quantity }}×</strong> {{ $item->product_name }}</li>
                                @endforeach
                            </ul>

                            @if ($order->notes)
                                <p class="order-notes"><span>Catatan</span>{{ $order->notes }}</p>
                            @endif

                            <div class="order-card-bottom">
                                <span class="order-total">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                                @php($nextStatus = ['baru' => 'diproses', 'diproses' => 'siap', 'siap' => 'selesai'][$queue['status']])
                                @php($nextLabel = ['baru' => 'Mulai dikerjakan', 'diproses' => 'Tandai siap', 'siap' => 'Tandai selesai'][$queue['status']])
                                <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $nextStatus }}">
                                    <button class="order-next-button" type="submit">{{ $nextLabel }} <span aria-hidden="true">→</span></button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="queue-empty">
                            <span class="queue-empty-mark" aria-hidden="true">✓</span>
                            <p>Tidak ada pesanan di tahap ini.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        @endforeach
    </div>

    <script>
        window.setTimeout(() => window.location.reload(), 15000);
    </script>
@endsection
