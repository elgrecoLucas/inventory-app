<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Tag::create([
            "name" => 'Vaso térmico',
            'slug' => 'vaso-termico',
        ]);
        Tag::create([
            "name" => 'vaso',
            'slug' => 'vaso',
        ]);
        Tag::create([
            "name" => 'Oficina',
            'slug' => 'oficina',
        ]);
        Tag::create([
            "name" => 'Acero inoxidable',
            'slug' => 'acero-inoxidable',
        ]);
        Tag::create([
            "name" => 'Pava eléctrica',
            'slug' => 'pava-electrica',
        ]);
        Tag::create([
            "name" => 'Cocina',
            'slug' => 'cocina',
        ]);

        DB::table('product_tag')->insert([
            "product_id" => 1, 
            "tag_id" => 1
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 1, 
            "tag_id" => 2
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 1, 
            "tag_id" => 3
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 1, 
            "tag_id" => 4
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 2, 
            "tag_id" => 3
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 2, 
            "tag_id" => 4
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 2, 
            "tag_id" => 5
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 2, 
            "tag_id" => 6
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 3, 
            "tag_id" => 1
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 3, 
            "tag_id" => 2
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 3, 
            "tag_id" => 3
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 3, 
            "tag_id" => 4
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 4, 
            "tag_id" => 1
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 4, 
            "tag_id" => 2
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 4, 
            "tag_id" => 3
        ]);
        DB::table('product_tag')->insert([
            "product_id" => 4, 
            "tag_id" => 4
        ]);
    }
}
