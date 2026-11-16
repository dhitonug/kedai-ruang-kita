<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedInteger('price');
            $table->boolean('available')->default(true);
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('source')->default('Meja');
            $table->unsignedInteger('table_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('total');
            $table->string('status')->default('baru');
            $table->timestamps();
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('unit_price');
            $table->unsignedInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
    }
};
