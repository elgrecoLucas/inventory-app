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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;

use Filament\Forms\Set;
use Filament\Forms\Get;

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

                Forms\Components\Section::make("Información del producto")->schema([

                    Forms\Components\TextInput::make('name')
                        ->label('Nombre del producto')
                        ->required()
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

                    Forms\Components\MarkdownEditor::make('description')
                        ->required()
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory('products'),
                ])->columns(2),
                
                Forms\Components\Section::make("Imágenes del producto")->schema([
                    Forms\Components\FileUpload::make('images')
                        ->multiple()
                        ->directory('products')
                        ->maxFiles(5)
                        ->reorderable()
                ])
            ])->columnSpan(2),
            
            Forms\Components\Group::make()->schema([
                Forms\Components\Section::make("Precio")->schema([
                    Forms\Components\TextInput::make('price')
                        ->label('Pesos')
                        ->numeric()
                        ->required(),
                ]),
                Forms\Components\Section::make("Relaciones")->schema([
                    Forms\Components\Select::make('category_id')
                        ->label('Categoria del producto')
                        ->relationship('category', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->reactive()
                ]),
                Forms\Components\Section::make("Estados")->schema([
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Es Premium/destacado')
                        ->required()
                        ->default(false),
                    Forms\Components\Toggle::make('in_stock')
                        ->label('En Stock')
                        ->required()
                        ->default(true),
                    Forms\Components\Toggle::make('on_sale')
                        ->label('En Oferta')
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
                Tables\Columns\TagsColumn::make('tags'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
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
                    ->label('En oferta')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                ->label('Imágenes'),
            ], position: ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            //'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
