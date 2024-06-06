<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Crear orden de compra';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'processing';
        $data['user_id'] = auth()->id();

        return $data;
    }
}
