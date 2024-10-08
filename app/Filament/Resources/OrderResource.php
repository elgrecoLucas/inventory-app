<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Enums\ActionsPosition;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class; 
    
    protected static ?string $navigationGroup = 'Gestión de compras';
    protected static ?string $navigationLabel = 'Ordenes de compra';
    //protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Información de la Compra')->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Usuario')
                        ->required()
                        ->columnSpan(6),
                    Forms\Components\TextInput::make('total_amount')
                        ->required()
                        ->label('Monto Total')
                        ->numeric()
                        ->columnSpan(4),
                    Forms\Components\TextInput::make('status')
                        ->required()
                        ->label('Estado')
                        ->columnSpan(4),
                    Forms\Components\TextInput::make('shipping_method')
                        ->label('Método de Envío')
                        ->required()
                        ->columnSpan(6),
                ])->columns(20),
                Forms\Components\Section::make('Detalle de la Compra')->schema([
                    Forms\Components\Repeater::make('orderItems')
                    ->relationship()
                    ->label('Ítems de la Orden')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                        ->relationship('product', 'name')
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} | {$record->color}")
                        ->label('Producto')
                        ->columnSpan(10),
                        Forms\Components\TextInput::make('quantity')
                        ->numeric()
                        ->label('Cantidad')
                        ->columnSpan(2),
                        Forms\Components\TextInput::make('unit_amount')
                          ->numeric()
                          ->label('Precio por Unidad')
                          ->columnSpan(4),
                        Forms\Components\TextInput::make('total_amount')
                          ->numeric()
                          ->label('Monto Total')
                          ->columnSpan(4)
                        ])->columns(20)
                    ])
                ])->columnSpanFull()
           
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nombre del Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.lastname')
                    ->label('Apellido del Usuario')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money()
                    ->label('Monto Total')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                ->label('Estado')
                ->colors([
                    'warning' => 'Procesando',
                    'success' => 'Finalizada',
                    'danger' => 'Cancelada',
                ])
                ->searchable(),
                Tables\Columns\TextColumn::make('shipping_method')
                ->label('Método de Envío'),
                Tables\Columns\TextColumn::make('user.address')
                ->tooltip('Hace click para copiar la dirección')
                ->label('Dirección')
                ->copyable()
                ->copyMessage('Dirección copiada!')
                ->copyMessageDuration(1500),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    //->toggleable(isToggledHiddenByDefault: true),
                    ->hidden(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    //->toggleable(isToggledHiddenByDefault: true),
                    ->hidden(),
            ])
            ->filters([
                //
            ])
            ->actions([
               /* ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),*/
                Tables\Actions\ViewAction::make()
                    ->label('Ver detalle')
                    ->modalHeading('Vista de Orden de Compra'),  
                Action::make('Aprobar')
                    ->action(function (Order $record) {
                        if($record->status == 'Procesando') {
                            $items = OrderItem::where('order_id', $record->id)->get();

                            foreach ($items as $item) {
                                $stock = Stock::find($item->stock_id);
                                $stock_update = $stock->stock_quantity_real - $item->quantity;

                                if($stock_update==0) {
                                    $stock->update([
                                        "stock_available" => false
                                    ]);
                                }
                                $stock->update([
                                    "stock_quantity_real" => $stock_update
                                ]);
                            }

                            $record->update([
                                "status" => "Finalizada"
                            ]);

                            Notification::make()
                            ->success()
                            ->title('Orden Aprobada!')
                            ->body('El stock real de los productos fue actualizado.')
                            ->send();

                        } else {
                            Notification::make()
                            ->danger()
                            ->title('No se puede aprobar la orden')
                            ->body('Sólo se pueden aprobar las ordenes en estado "Procesando"')
                            ->send();
                        }
                      
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-hand-thumb-up')
                    ->button()
                    ->color('success'),

                Action::make('Cancelar')
                    ->action(function (Order $record) {
                        
                        if($record->status == 'Procesando') {
                            $items = OrderItem::where('order_id', $record->id)->get();

                            foreach ($items as $item) {
                                $stock = Stock::find($item->stock_id);
                                $stock_update = $stock->stock_quantity_virtual + $item->quantity;

                                if(!$stock->stock_available) {
                                    $stock->update([
                                        "stock_available" => true
                                    ]);
                                }

                                $stock->update([
                                    "stock_quantity_virtual" => $stock_update
                                ]);
                            }

                            $record->update([
                                "status" => "Cancelada"
                            ]);

                            Notification::make()
                            ->success()
                            ->title('Orden Cancelada!')
                            ->body('El stock de los productos fue actualizado.')
                            ->send();
                        } else {
                            Notification::make()
                            ->danger()
                            ->title('No se puede cancelar la orden')
                            ->body('Sólo se pueden cancelar las ordenes en estado "Procesando"')
                            ->send();
                        }
                        
                    })
                    ->requiresConfirmation()
                    ->icon('heroicon-o-hand-thumb-down')
                    ->color('danger'),               
                ]);
            /*->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);*/
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            //'create' => Pages\CreateOrder::route('/create'),
            //'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
    public static function getBreadcrumb(): string
    {
        return 'Ordenes de Compra';
    }
}
