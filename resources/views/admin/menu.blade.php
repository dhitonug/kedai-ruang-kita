@extends('layouts.admin')
@section('title', 'Menu · Kedai Ruang Kita')
@section('crumb', 'Menu digital')

@section('content')
    <div class="page-head">
        <div>
            <h1>Menu digital</h1>
            <p>Kelola katalog dan ketersediaan menu yang tampil ke pelanggan.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="success" role="status">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="validation-errors" role="alert">
            <strong>Periksa kembali data menu:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panels menu-panels">
        <section class="panel">
            <h2>{{ $editingProduct ? 'Edit menu' : 'Tambah menu baru' }}</h2>
            <p class="panel-sub">
                {{ $editingProduct ? 'Perbarui informasi yang dilihat pelanggan.' : 'Menu baru akan langsung muncul di katalog pelanggan.' }}
            </p>

            <form method="POST" action="{{ $editingProduct ? route('admin.menu.update', $editingProduct) : route('admin.menu.save') }}">
                @csrf
                @if ($editingProduct)
                    @method('PATCH')
                @endif

                <div class="form-grid">
                    <div class="field full">
                        <label for="menu-name">Nama menu</label>
                        <input id="menu-name" name="name" value="{{ old('name', $editingProduct?->name) }}" required maxlength="120" placeholder="Contoh: Nasi Ayam Bakar">
                    </div>

                    <div class="field">
                        <label for="menu-category">Kategori</label>
                        <select id="menu-category" name="category" required>
                            @php($category = old('category', $editingProduct?->category ?? 'Makanan Utama'))
                            @foreach (['Makanan Utama', 'Minuman', 'Camilan'] as $categoryOption)
                                <option value="{{ $categoryOption }}" @selected($category === $categoryOption)>{{ $categoryOption }}</option>
                            @endforeach
                            @if (!in_array($category, ['Makanan Utama', 'Minuman', 'Camilan'], true))
                                <option value="{{ $category }}" selected>{{ $category }}</option>
                            @endif
                        </select>
                    </div>

                    <div class="field">
                        <label for="menu-price">Harga (Rp)</label>
                        <input id="menu-price" type="number" min="1000" step="1000" name="price" value="{{ old('price', $editingProduct?->price) }}" required placeholder="28000">
                    </div>

                    <div class="field full">
                        <label for="menu-description">Deskripsi singkat</label>
                        <textarea id="menu-description" name="description" rows="3" maxlength="500" placeholder="Bahan dan cita rasa">{{ old('description', $editingProduct?->description) }}</textarea>
                    </div>

                    <div class="field full">
                        <label for="menu-image">URL foto</label>
                        <input id="menu-image" name="image_url" type="url" maxlength="500" value="{{ old('image_url', $editingProduct?->image_url) }}" placeholder="https://...">
                    </div>
                </div>

                <div class="menu-form-actions">
                    <button class="btn" type="submit">{{ $editingProduct ? 'Simpan perubahan' : '+ Simpan menu' }}</button>
                    @if ($editingProduct)
                        <a class="btn light" href="{{ route('admin.menu') }}">Batal</a>
                    @endif
                </div>
            </form>
        </section>

        <section class="panel menu-list-panel">
            <div class="menu-list-heading">
                <div>
                    <h2>Daftar menu</h2>
                    <p class="panel-sub">{{ $products->count() }} item menu terdaftar</p>
                </div>
            </div>

            <div class="table-wrap menu-table-wrap">
                <table class="table menu-table">
                    <thead>
                        <tr>
                            <th>MENU</th>
                            <th>KATEGORI</th>
                            <th>HARGA</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    <div class="menu-product-cell">
                                        @if ($product->image_url)
                                            <img class="menu-product-image" src="{{ $product->image_url }}" alt="" loading="lazy">
                                        @else
                                            <span class="menu-product-placeholder" aria-hidden="true">RK</span>
                                        @endif
                                        <b>{{ $product->name }}</b>
                                    </div>
                                </td>
                                <td>{{ $product->category }}</td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $product->available ? 'siap' : 'dibatalkan' }}">
                                        {{ $product->available ? 'Tersedia' : 'Habis' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="menu-actions">
                                        <form method="POST" action="{{ route('admin.menu.stock', $product) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn light small" type="submit">{{ $product->available ? 'Tandai habis' : 'Aktifkan' }}</button>
                                        </form>
                                        <a class="btn light small" href="{{ route('admin.menu.edit', $product) }}">Edit</a>
                                        <form method="POST" action="{{ route('admin.menu.delete', $product) }}" onsubmit="return confirm('Hapus menu ini? Riwayat pesanan tetap tersimpan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn small danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="menu-empty">Belum ada menu. Tambahkan menu pertama melalui formulir ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
