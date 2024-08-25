<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ProductResource\Pages;
use App\Filament\App\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Str;
//use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;

use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\TextInput;

use App\Models\Category;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationLabel = 'Listado de productos';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Group::make()->schema([

                Forms\Components\Section::make("Información del Producto")->schema([

                    Forms\Components\TextInput::make('name')
                        ->label('Nombre del Producto')
                        ->required()
                        ->suffixAction(
                            Action::make('copyCostToPrice')
                                ->icon('heroicon-m-clipboard')
                                ->color('warning')
                                ->action(function ($livewire, $state) {
                                    $livewire->js(
                                        'window.navigator.clipboard.writeText("'.$state.'");
                                        $tooltip("'.__('Nombre copiado!').'", { timeout: 1500 });'
                                    );
                                })
                            )
                        ->maxLength(255),

                    Forms\Components\TextInput::make('brand')
                        ->label('Marca')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('model')
                        ->label('Modelo')
                        ->required()
                        ->maxLength(255),
                    
                    Forms\Components\TextInput::make('color')
                        ->label("Color")
                        ->required(),
                    Forms\Components\TextInput::make('size')
                        ->label("Tamaño/Capacidad")
                        ->required(),

                    Forms\Components\TextInput::make('description')
                        ->required()
                        ->columnSpanFull()
                        ->suffixAction(
                            Action::make('copyCostToPrice')
                                ->icon('heroicon-m-clipboard')
                                ->color('warning')
                                ->action(function ($livewire, $state) {
                                    $livewire->js(
                                        'window.navigator.clipboard.writeText("'.$state.'");
                                        $tooltip("'.__('Descripción copiada!').'", { timeout: 1500 });'
                                    );
                                })
                            ),
                ])->columns(2),
                
                Forms\Components\Section::make("Imágenes del producto")->schema([
                    Forms\Components\FileUpload::make('images')
                        ->label('Imágenes')
                        ->multiple()
                        ->directory('products')
                        ->maxFiles(10)
                        ->reorderable()
                        ->downloadable(),
                ])
            ])->columnSpan(2),
            
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make("Precios")->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Precio Mayorista')
                        ->numeric()
                        ->required(),
                        Forms\Components\TextInput::make('suggest_price')
                        ->label('Precio Sugerido para la venta')
                        ->numeric()
                        ->required(),
                ]),
                Forms\Components\Section::make("Relaciones")->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Categoría del Producto')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->reactive()
                ]),
                Forms\Components\Section::make("Estados")->schema([
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Es Destacado?')
                        ->required()
                        ->default(false),
                    Forms\Components\Toggle::make('in_stock')
                        ->label('En Stock?')
                        ->required()
                        ->default(true),
                    Forms\Components\Toggle::make('on_sale')
                        ->label('En Oferta?')
                        ->required()
                        ->default(false),
                ]),
            ])->columnSpan(1)

        ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->description(fn (Product $record): string => Str::limit($record->description, 50, '...'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label('Modelo')
                    ->searchable(),
                Tables\Columns\TagsColumn::make('tags.name')
                    ->label('Etiquetas'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio Mayorista')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suggest_price')
                    ->label('Precio Sugerido')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color')        
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->label('Tamaño')    
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock.stock_quantity_virtual')
                    ->label('Stock')
                    ->numeric(),
                Tables\Columns\IconColumn::make('on_sale')
                    ->label('En Oferta?')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Es Destacado?')
                    ->boolean(),
            ])
            ->filters([
                Filter::make('in_stock')
                    ->query(fn (Builder $query): Builder => $query->where('in_stock', true))
                    ->label('En stock')
                    ->default()
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('Ver más')
                ->modalHeading('Vista de Producto'),
            ], position: ActionsPosition::BeforeColumns);
           /* ->bulkActions([
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
            'index' => Pages\ListProducts::route('/'),
            //'create' => Pages\CreateProduct::route('/create'),
            //'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
    public static function getBreadcrumb(): string
    {
        return 'Productos';
    }
}
