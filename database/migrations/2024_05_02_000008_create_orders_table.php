<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * | El user_id es del vendedor/cliente que realiza la compra. Es decir, genera una Order.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('total_amount');
            $table->enum('status', ['Procesando', 'Finalizada', 'Cancelada'])->default('processing');
            $table->enum('shipping_method', ['Entrega a domicilio', 'El vendedor entrega', 'Recoger en la oficina']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
