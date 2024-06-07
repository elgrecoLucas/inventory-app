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
                        ->label('Tipo de entrega')
                        ->default('home delivery')
                        ->required()
                        ->options([
                            'home delivery' => 'Entrega a domicilio',
                            'the seller delivers' => 'El vendedor entrega',
                            'pick up at the office' => 'Recoger en la oficina',
                        ])
                        ->colors([
                            'home delivery' => 'info',
                            'the seller delivers' => 'info',
                            'pick up at the office' => 'info',
                        ])
                        ->icons([
                            'home delivery' => 'heroicon-m-home',
                            'the seller delivers' => 'heroicon-m-truck',
                            'pick up at the office' => 'heroicon-m-building-office-2',
                        ])
                        ->columnSpan(8),
                    ])->columns(12),
                        
                    Forms\Components\Section::make('Compra')->schema([
                        Forms\Components\Repeater::make('orderItems')
                        ->relationship()
                        ->label('productos')
                        ->schema([
                            Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
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
                            ->label('Stock disponible')
                            ->disabled()
                            ->hidden(fn (Get $get) => ! $get('product_id'))
                            ->columnSpan(2),

                            Forms\Components\TextInput::make('unit_amount')
                            ->numeric()
                            ->label('Precio')
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
                            ->label('Precio x cantidad')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->hidden(fn (Get $get) => ! $get('product_id'))
                            ->columnSpan(2)
                        ])->columns(12)->addActionLabel('Agregar producto'),

                        Forms\Components\Placeholder::make('total_amount_placeholder')
                        ->label('Precio total de la orden de compra')
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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.lastname')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('shipping_method'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}