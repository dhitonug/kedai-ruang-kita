<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'table_number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'customer_name' => ['nullable', 'string', 'max:80'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $order = DB::transaction(function () use ($data) {
            $products = Product::whereIn('id', collect($data['items'])->pluck('id'))->where('available', true)->get()->keyBy('id');
            abort_if($products->count() !== count($data['items']), 422, 'Ada menu yang sudah habis. Muat ulang halaman.');
            $total = collect($data['items'])->sum(fn ($item) => $products[$item['id']]->price * $item['quantity']);
            $order = Order::create([
                'code' => 'RK-' . now()->format('ymd') . '-' . Str::upper(Str::random(4)),
                'source' => 'Meja', 'table_number' => $data['table_number'] ?? null,
                'customer_name' => $data['customer_name'] ?? null, 'notes' => $data['notes'] ?? null,
                'total' => $total, 'status' => 'baru',
            ]);
            foreach ($data['items'] as $item) {
                $product = $products[$item['id']];
                $order->items()->create(['product_id' => $product->id, 'product_name' => $product->name, 'quantity' => $item['quantity'], 'unit_price' => $product->price, 'subtotal' => $product->price * $item['quantity']]);
            }
            return $order;
        });
        return redirect()->route('orders.show', $order)->with('success', 'Pesanan berhasil dikirim ke kasir.');
    }

    public function show(Order $order)
    {
        $order->load('items');
        return view('order-confirmation', compact('order'));
    }
}
