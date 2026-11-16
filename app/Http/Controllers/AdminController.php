<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function login() { return view('admin.login'); }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if ($credentials['email'] === config('ruangkita.admin_email') && hash_equals(config('ruangkita.admin_password'), $credentials['password'])) {
            $request->session()->regenerate();
            $request->session()->put('admin_authenticated', true);
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai.'])->onlyInput('email');
    }

    public function logout(Request $request) { $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect()->route('admin.login'); }

    public function dashboard(Request $request)
    {
        $period = $request->query('periode', 'hari');
        $from = match ($period) { 'minggu' => now()->startOfWeek(), 'bulan' => now()->startOfMonth(), default => now()->startOfDay() };
        $orders = Order::where('created_at', '>=', $from)->whereIn('status', ['siap', 'selesai'])->get();
        $revenue = $orders->sum('total');
        $count = $orders->count();
        $bestSellers = \App\Models\OrderItem::selectRaw('product_name, SUM(quantity) as units')->whereIn('order_id', $orders->pluck('id'))->groupBy('product_name')->orderByDesc('units')->take(5)->get();
        $recentOrders = Order::with('items')->latest()->take(6)->get();
        $pending = Order::whereIn('status', ['baru', 'diproses', 'siap'])->count();
        $dailySales = collect(range(6, 0))->map(fn ($days) => ['label' => now()->subDays($days)->translatedFormat('D'), 'value' => Order::whereDate('created_at', now()->subDays($days)->toDateString())->whereIn('status', ['siap', 'selesai'])->sum('total')]);
        return view('admin.dashboard', compact('period', 'revenue', 'count', 'bestSellers', 'recentOrders', 'pending', 'dailySales'));
    }

    public function orders(Request $request)
    {
        $baru = Order::with('items')->where('status', 'baru')->oldest('created_at')->oldest('id')->get();
        $diproses = Order::with('items')->where('status', 'diproses')->oldest('created_at')->oldest('id')->get();
        $siap = Order::with('items')->where('status', 'siap')->oldest('created_at')->oldest('id')->get();
        $activeCount = $baru->count() + $diproses->count() + $siap->count();
        return view('admin.orders-board', compact('baru', 'diproses', 'siap', 'activeCount'));
    }

    public function orderHistory(Request $request)
    {
        $status = in_array($request->query('status'), ['selesai', 'dibatalkan'], true) ? $request->query('status') : null;
        $orders = Order::with('items')
            ->whereIn('status', $status ? [$status] : ['selesai', 'dibatalkan'])
            ->latest()
            ->paginate(20);

        return view('admin.orders', compact('orders', 'status'));
    }

    public function updateOrder(Request $request, Order $order)
    {
        $data = $request->validate(['status' => ['required', 'in:baru,diproses,siap,selesai,dibatalkan']]);
        $allowedTransitions = [
            'baru' => ['diproses', 'dibatalkan'],
            'diproses' => ['siap', 'dibatalkan'],
            'siap' => ['selesai'],
            'selesai' => [],
            'dibatalkan' => [],
        ];

        if (!in_array($data['status'], $allowedTransitions[$order->status] ?? [], true)) {
            return back()->with('error', 'Status pesanan sudah berubah atau tidak bisa dilanjutkan dari tahap ini. Muat ulang antrean.');
        }

        $order->update($data);
        return back()->with('success', 'Pesanan ' . $order->code . ' dipindahkan ke tahap ' . $order->status . '.');
    }

    public function menu()
    {
        $products = Product::orderBy('category')->orderBy('name')->get();
        $editingProduct = null;

        return view('admin.menu', compact('products', 'editingProduct'));
    }

    public function editMenu(Product $product)
    {
        $products = Product::orderBy('category')->orderBy('name')->get();
        $editingProduct = $product;

        return view('admin.menu', compact('products', 'editingProduct'));
    }

    public function tables() { $tables = range(1, 12); return view('admin.tables', compact('tables')); }

    public function saveMenu(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'category' => ['required', 'string', 'max:60'], 'price' => ['required', 'integer', 'min:1000'], 'description' => ['nullable', 'string', 'max:500'], 'image_url' => ['nullable', 'url', 'max:500']]);
        Product::create($data + ['available' => true]);
        return back()->with('success', 'Menu baru ditambahkan.');
    }

    public function updateMenu(Request $request, Product $product)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'category' => ['required', 'string', 'max:60'], 'price' => ['required', 'integer', 'min:1000'], 'description' => ['nullable', 'string', 'max:500'], 'image_url' => ['nullable', 'url', 'max:500']]);
        $product->update($data);

        return redirect()->route('admin.menu')->with('success', 'Perubahan menu berhasil disimpan.');
    }

    public function deleteMenu(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.menu')->with('success', 'Menu berhasil dihapus. Riwayat pesanan tetap tersimpan.');
    }

    public function toggleStock(Product $product)
    {
        $product->update(['available' => !$product->available]);
        return back()->with('success', 'Ketersediaan menu diperbarui.');
    }

    public function channels() { return view('admin.channels'); }

    public function createExternalOrder(Request $request)
    {
        $data = $request->validate(['source' => ['required', 'in:GoFood,GrabFood,ShopeeFood'], 'code' => ['nullable', 'string', 'max:80'], 'total' => ['required', 'integer', 'min:1000'], 'notes' => ['nullable', 'string', 'max:300']]);
        $order = Order::create(['code' => $data['code'] ?: 'RK-' . now()->format('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4)), 'source' => $data['source'], 'total' => $data['total'], 'notes' => $data['notes'] ?? null, 'status' => 'baru']);
        $order->items()->create(['product_name' => 'Pesanan ' . $data['source'], 'quantity' => 1, 'unit_price' => $data['total'], 'subtotal' => $data['total']]);
        return redirect()->route('admin.orders')->with('success', 'Pesanan marketplace masuk ke daftar pesanan pusat.');
    }
}
