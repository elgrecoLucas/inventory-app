<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class ProductResource extends Resource
{   
    protected static ?string $navigationGroup = 'Gestión de productos';
    protected static ?string $model = Product::class;
    protected static ?string $navigationLabel = 'Productos';
    protected static ?int $navigationSort = 2;
    //protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([

                    Forms\Components\Section::make("Información del producto")->schema([
                        Forms\Components\Placeholder::make('Importante')
                            ->content(new HtmlString('<h3 style="color: red; font-weight: bold;">En el nombre del producto hay que especificar el color y el tamaño. Por ejemplo: "Vaso Térmico 1,18 Lts | Color Merlot"</h3>')),

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
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio')
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                Tables\Columns\IconColumn::make('in_stock')
                    ->label('Stock')
                    ->boolean(),
                Tables\Columns\IconColumn::make('on_sale')
                    ->label('Oferta')
                    ->boolean(),
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
                Tables\Actions\ViewAction::make()
                ->label('Ver'),
                Tables\Actions\EditAction::make()
                ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                ->label(''),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
            /*
            ->recordUrl(
                fn (Product $record): string => Pages\ViewProduct::getUrl([$record->id]),
            );*/
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
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            //'view' => Pages\ViewProduct::route('/{record}/view'),
        ];
    }
}
