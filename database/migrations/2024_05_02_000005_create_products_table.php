<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * | is_active lo deshabilito, no puedo cargar más stock para ese producto
     * | is_featured es para agregarlo a una sección premium/destacarlo
     * | in_stock SE PUEDE ELIMINAR...NO ESTOY SEGURO SI ES ÚTIL
     * | on_sale si esta en oferta
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->json('images')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->decimal('price');
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->string('description');
            $table->boolean('is_featured')->default(false);
            $table->boolean('in_stock')->default(true);
            $table->boolean('on_sale')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
