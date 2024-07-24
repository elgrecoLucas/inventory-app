<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

use App\Models\Stock;
use App\Models\Product;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $productCreated = $this->record;

        $category_id = $productCreated->category_id;
        
        $stock = Stock::where('product_id', $productCreated->id)->update(['category_id' => $category_id]);

    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
