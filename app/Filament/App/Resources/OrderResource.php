<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\OrderResource\Pages;
use App\Filament\App\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Closure;
use Illuminate\Support\Number;

use App\Models\Product;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationLabel = 'Mis compras';
    //protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::user()->id);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Información')->schema([
                        Forms\Components\TextInput::make('user_name')
                        ->default(auth()->user()->name)
                        ->label('Comprador')
                        ->disabled()
                        ->columnSpan(4),
                        Forms\Components\ToggleButtons::make('shipping_method')
                        ->inline()
                        ->label('Método de Envío')
                        ->default('Entrega a domicilio')
                        ->required()
                        ->options([
                            'Entrega a domicilio' => 'Entrega a domicilio',
                            'El vendedor entrega' => 'El vendedor entrega',
                            'Recoger en la oficina' => 'Recoger en la oficina',
                        ])
                        ->colors([
                            'Entrega a domicilio' => 'info',
                            'El vendedor entrega' => 'info',
                            'Recoger en la oficina' => 'info',
                        ])
                        ->icons([
                            'Entrega a domicilio' => 'heroicon-m-home',
                            'El vendedor entrega' => 'heroicon-m-truck',
                            'Recoger en la oficina' => 'heroicon-m-building-office-2',
                        ])
                        ->columnSpan(8),
                    ])->columns(12),
                        
                    Forms\Components\Section::make('Compra')->schema([
                        Forms\Components\Repeater::make('orderItems')
                        ->relationship()
                        ->label('Ítems de la Orden')
                        ->schema([
                            Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name',fn (Builder $query) => $query->join('stocks', 'products.id', '=', 'stocks.product_id')->where('stock_quantity_real','>',0)->where('in_stock',true))
                            ->getOptionLabelFromRecordUsing(fn (Model $record) => "{$record->name} | {$record->color}")
                            ->label('Producto')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->reactive()
                            ->afterStateUpdated(fn($state, Set $set) => $set('unit_amount', Product::find($state)?->price ?? 0))
                            ->afterStateUpdated(fn($state, Set $set) => $set('total_amount', Product::find($state)?->price ?? 0))
                            ->afterStateUpdated(function($state, Set $set) {
                                $set('stock_id', Product::find($state)->stock->id);
                                $set('stock_quantity_virtual', Product::find($state)->stock->stock_quantity_virtual);
                            })
                            ->afterStateUpdated(fn($state, Set $set) => $set('category_id', Product::find($state)->category->id))
                            ->selectablePlaceholder(false)
                            ->columnSpan(4),

                            Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->label('Cantidad')
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                    if ($value > $get('stock_quantity_virtual')) {
                                        $fail('La :attribute no puede ser superior al stock disponible');
                                    }
                                },
                            ])
                            ->afterStateUpdated(function ($state, Set $set, Get $get, Forms\Contracts\HasForms $livewire, Forms\Components\TextInput $component) {
                                $livewire->validateOnly($component->getStatePath());
                                $set('total_amount', $state*$get('unit_amount'));
                            })
                            ->hidden(fn (Get $get) => ! $get('product_id'))
                            ->columnSpan(2),

                            Forms\Components\TextInput::make('stock_quantity_virtual')
                            ->numeric()
                            ->label('Stock Disponible')
                            ->disabled()
                            ->hidden(fn (Get $get) => ! $get('product_id'))
                            ->columnSpan(2),

                            Forms\Components\TextInput::make('unit_amount')
                            ->numeric()
                            ->label('Precio por Unidad')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->hidden(fn (Get $get) => ! $get('product_id'))
                            ->columnSpan(2),

                            Forms\Components\Hidden::make('stock_id')
                            ->default(0),

                            Forms\Components\Hidden::make('category_id')
                            ->default(0),

                            Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->label('Monto Total')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->hidden(fn (Get $get) => ! $get('product_id'))
                            ->columnSpan(2)
                        ])->columns(12)->addActionLabel('Agregar producto'),

                        Forms\Components\Placeholder::make('total_amount_placeholder')
                        ->label('MONTO TOTAL DE LA ORDEN DE COMPRA')
                        ->content(function(Get $get, Set $set) {
                            $total = 0;
                            if (!$repeaters = $get('orderItems')){
                                return $total;
                            }

                            foreach ($repeaters as $key => $repeater){
                                $total += $get("orderItems.{$key}.total_amount");
                            }

                            $set('total_amount', $total);
                            return Number::currency($total);
                        }),

                        Forms\Components\Hidden::make('total_amount')
                        ->default(0)
                    ])

                ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nombre/s')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.lastname')
                    ->label('Apellido/s')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Monto Total')
                    ->money()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'warning' => 'Procesando',
                        'success' => 'Finalizada',
                        'danger' => 'Cancelada',
                    ]),
                Tables\Columns\TextColumn::make('shipping_method')
                    ->label('Método de Envío'),
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
            ]);
            /*->actions([
                Tables\Actions\EditAction::make()
                ->label('Editar'),
            ]);
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);*/
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            //'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
    public static function getBreadcrumb(): string
    {
        return 'Ordenes de Compra';
    }
}