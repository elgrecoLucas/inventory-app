<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'category_id' => 3,
                'name' => 'Vaso Térmico Stanley Quencher 1,18 Lts',
                'images' => json_encode(['imagen1.jpg', 'imagen2.jpg']), 
                'brand' => 'Stanley',
                'model' => 'Quencher',
                'price' => 59.99,
                'color' => 'Merlot',
                'size' => '1.18 Lts',
                'description' => 'Vaso térmico Stanley Quencher de acero inoxidable 18/8 con doble pared aislante. Mantiene tus bebidas frías por 6 horas y calientes por 4 horas. Tapa FlowState™ a prueba de fugas. Ideal para llevar contigo a cualquier lugar.',
                'is_featured' => true, // Producto destacado
                'in_stock' => true,
                'on_sale' => false, // Producto en oferta
            ],
            [
                'category_id' => 2,
                'name' => 'Pava Eléctrica Inoxidable 1.7 Lts',
                'images' => json_encode(['imagen3.jpg', 'imagen4.jpg']), 
                'brand' => 'Acme', 
                'model' => 'PE-1700', 
                'price' => 39.99,
                'color' => 'Plata',
                'size' => '1.7 Lts',
                'description' => 'Pava eléctrica de acero inoxidable con capacidad de 1.7 litros. Control de temperatura regulable. Apagado automático. Base giratoria 360°. Filtro removible para facilitar la limpieza. Ideal para el hogar o la oficina.',
                'is_featured' => false,
                'in_stock' => true,
                'on_sale' => false,
            ],
            [
                'category_id' => 3,
                'name' => 'Vaso Térmico Stanley Quencher 1,18 Lts',
                'images' => json_encode(['imagen1.jpg', 'imagen2.jpg']), 
                'brand' => 'Stanley',
                'model' => 'Quencher',
                'price' => 59.99,
                'color' => 'Gris',
                'size' => '1.18 Lts',
                'description' => 'Vaso térmico Stanley Quencher de acero inoxidable 18/8 con doble pared aislante. Mantiene tus bebidas frías por 6 horas y calientes por 4 horas. Tapa FlowState™ a prueba de fugas. Ideal para llevar contigo a cualquier lugar.',
                'is_featured' => true, // Producto destacado
                'in_stock' => true,
                'on_sale' => false, // Producto en oferta
            ],
            [
                'category_id' => 3,
                'name' => 'Vaso Térmico Stanley Quencher 1,18 Lts',
                'images' => json_encode(['imagen1.jpg', 'imagen2.jpg']), 
                'brand' => 'Stanley',
                'model' => 'Quencher',
                'price' => 59.99,
                'color' => 'Acero',
                'size' => '1.18 Lts',
                'description' => 'Vaso térmico Stanley Quencher de acero inoxidable 18/8 con doble pared aislante. Mantiene tus bebidas frías por 6 horas y calientes por 4 horas. Tapa FlowState™ a prueba de fugas. Ideal para llevar contigo a cualquier lugar.',
                'is_featured' => true, // Producto destacado
                'in_stock' => true,
                'on_sale' => false, // Producto en oferta
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
