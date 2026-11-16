<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['Nasi Goreng Kampung', 'Makanan Utama', 'Nasi goreng dengan ayam suwir, telur mata sapi, acar, dan kerupuk.', 'https://images.unsplash.com/photo-1696340030298-a7f877a7780c?auto=format&fit=crop&w=900&q=85', 28000],
            ['Ayam Bakar Madu', 'Makanan Utama', 'Ayam bakar bumbu madu, sambal terasi, dan nasi hangat.', 'https://images.unsplash.com/photo-1532550907401-a500c9a57435?auto=format&fit=crop&w=900&q=85', 35000],
            ['Mie Goreng Jawa', 'Makanan Utama', 'Mie goreng rumahan dengan sayur segar dan telur.', 'https://images.unsplash.com/photo-1647102215880-55159ecc1e35?auto=format&fit=crop&w=900&q=85', 26000],
            ['Soto Ayam', 'Makanan Utama', 'Soto ayam bening dengan koya, soun, dan perkedel.', 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=85', 27000],
            ['Es Kopi Ruang', 'Minuman', 'Espresso, susu segar, dan gula aren racikan kami.', 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?auto=format&fit=crop&w=900&q=85', 22000],
            ['Matcha Latte', 'Minuman', 'Matcha premium dengan susu lembut dan creamy.', 'https://images.unsplash.com/photo-1515823064-d6e0c04616a7?auto=format&fit=crop&w=900&q=85', 25000],
            ['Es Teh Nusantara', 'Minuman', 'Teh melati pilihan disajikan dingin.', 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=900&q=85', 12000],
            ['Pisang Goreng', 'Camilan', 'Pisang kepok goreng renyah dengan madu.', 'https://images.unsplash.com/photo-1576867917480-152bca50166e?auto=format&fit=crop&w=900&q=85', 18000],
            ['Singkong Keju', 'Camilan', 'Singkong goreng empuk, taburan keju dan saus gula aren.', 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=900&q=85', 19000],
        ];
        foreach ($products as [$name, $category, $description, $image, $price]) {
            \App\Models\Product::firstOrCreate(['name' => $name], ['category' => $category, 'description' => $description, 'image_url' => $image, 'price' => $price, 'available' => true]);
        }
    }
}
