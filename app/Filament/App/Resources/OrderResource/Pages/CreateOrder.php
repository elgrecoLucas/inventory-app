<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Order;
use App\Models\Stock;
use App\Models\OrderItem;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Crear orden de compra';
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'Procesando';
        $data['user_id'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'La orden de compra se creo con éxito';
    }

    // Menejador de la creación: handleRecordCreation(array $data): Order

    protected function afterCreate(): void
    {
        // HAY UN ERROR IGUAL...tendría que funcionar el $orderCreated->orderItems
        // Creo que el Repeater, en el form del OrderResource, no esta bien implementado
        // Es raro porque si se guardan los orderItems en la DB
        // https://github.com/filamentphp/demo/blob/main/app/Filament/Resources/Shop/OrderResource.php
        $orderCreated = $this->record;
        
        $items = OrderItem::where('order_id', $orderCreated->id)->get();

        foreach ($items as $item) {
            $stock = Stock::find($item->stock_id);
            $stock_update = $stock->stock_quantity_virtual - $item->quantity;

            if($stock_update == 0) {
                $stock->update([
                    "stock_available" => false
                ]);
            }

            $stock->update([
                "stock_quantity_virtual" => $stock_update
            ]);
        }

    }
}
