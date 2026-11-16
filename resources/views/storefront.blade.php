@extends('layouts.store')

@section('title', 'Menu · Kedai Ruang Kita')

@section('content')
    <main>
        <section class="hero-wrap">
            <div class="container-xl">
                <div class="hero-panel">
                    <div class="hero-copy">
                        <p class="hero-kicker">Makan enak, duduk lebih lama</p>
                        <h1 class="hero-title">Ada tempat untuk kita.</h1>
                        <p class="hero-description">
                            Pilih makanan rumahan, kopi, dan camilan untuk menemani obrolanmu hari ini.
                        </p>
                        <div class="hero-actions">
                            <a class="btn-rk-primary" href="#menu">Pilih menu <span class="ms-2" aria-hidden="true">↓</span></a>
                            @if ($table)
                                <span class="table-context">Pesan untuk Meja {{ $table }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="hero-photo">
                        <img
                            src="https://images.unsplash.com/photo-1775831412138-ba1f18289e8f?auto=format&fit=crop&w=1400&q=85"
                            alt="Kedai kopi di Surabaya dengan area duduk"
                            fetchpriority="high"
                        >
                    </div>
                </div>
            </div>
        </section>

        <section class="menu-section" id="menu" aria-labelledby="menu-heading">
            <div class="container-xl">
                @if (session('success'))
                    <div class="notice-rk" role="status">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="notice-rk" role="alert">{{ $errors->first() }}</div>
                @endif

                <div class="section-heading">
                    <span class="section-eyebrow">Dari dapur kami</span>
                    <h2 class="section-title" id="menu-heading">Menu Kedai</h2>
                    <p class="section-description">Makanan utama, minuman, dan camilan untuk dinikmati bersama.</p>
                </div>

                <div class="menu-toolbar">
                    <nav class="category-list" aria-label="Saring menu berdasarkan kategori">
                        <button class="category-button" type="button" data-filter="Semua" aria-pressed="true">Semua menu</button>
                        @foreach ($categories as $category)
                            <button class="category-button" type="button" data-filter="{{ $category }}" aria-pressed="false">
                                {{ $category }}
                            </button>
                        @endforeach
                    </nav>
                    <span class="menu-count">{{ $products->count() }} pilihan</span>
                </div>

                <div class="menu-grid" id="menuGrid">
                    @forelse ($products as $product)
                        <article class="menu-card" data-category="{{ $product->category }}">
                            <div class="menu-image">
                                <img src="{{ $product->image_url ?: asset('images/menu-placeholder.svg') }}" alt="{{ $product->name }}" loading="lazy">
                            </div>
                            <div class="menu-card-body">
                                <p class="menu-category">{{ $product->category }}</p>
                                <h3 class="menu-title">{{ $product->name }}</h3>
                                <p class="menu-description">{{ $product->description }}</p>
                                <div class="menu-card-footer">
                                    <span class="menu-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <button
                                        class="add-menu-button"
                                        type="button"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        aria-label="Tambah {{ $product->name }} ke pesanan"
                                    >
                                        <span class="add-icon" aria-hidden="true">+</span>
                                        <span>Tambah</span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="empty-menu" role="status">Menu sedang disiapkan. Silakan kembali sebentar lagi.</p>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <button class="cart-dock" id="cartDock" type="button" aria-haspopup="dialog" aria-controls="cartDialog" hidden>
        <span class="cart-dock-summary" aria-live="polite">
            <span class="cart-dock-count" id="cartCount">0 item</span>
            <span class="cart-dock-total" id="cartTotal">Rp 0</span>
        </span>
        <span class="cart-dock-button">Lihat pesanan <span aria-hidden="true">→</span></span>
    </button>

    <dialog class="cart-dialog" id="cartDialog" aria-labelledby="cartTitle">
        <div class="cart-dialog-inner">
            <div class="cart-dialog-header">
                <div>
                    <h2 class="cart-dialog-title" id="cartTitle">Pesanan kamu</h2>
                    <p class="cart-dialog-hint">Periksa pilihanmu sebelum dikirim ke kedai.</p>
                </div>
                <button class="cart-close" id="closeCart" type="button" aria-label="Tutup keranjang">×</button>
            </div>

            <div class="cart-lines" id="cartLines" aria-live="polite"></div>

            <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
                @csrf
                @if ($table)
                    <input type="hidden" name="table_number" value="{{ $table }}">
                @else
                    <div class="checkout-field">
                        <label for="tableNumber">Nomor meja <span class="fw-normal text-secondary">(opsional)</span></label>
                        <input id="tableNumber" type="number" min="1" max="99" name="table_number" placeholder="Contoh: 4">
                    </div>
                @endif
                <div class="checkout-field">
                    <label for="customerName">Nama <span class="fw-normal text-secondary">(opsional)</span></label>
                    <input id="customerName" name="customer_name" maxlength="80" placeholder="Nama pemesan">
                </div>
                <div class="checkout-field">
                    <label for="orderNotes">Catatan untuk kedai</label>
                    <textarea id="orderNotes" name="notes" rows="2" maxlength="500" placeholder="Contoh: tidak pedas, es dipisah"></textarea>
                </div>
                <div id="itemInputs"></div>
                <div class="checkout-total">
                    <span>Total pesanan</span>
                    <span id="modalTotal">Rp 0</span>
                </div>
                <button class="checkout-submit" id="checkoutButton" type="submit" disabled>Kirim pesanan ke kasir</button>
            </form>
        </div>
    </dialog>
@endsection
