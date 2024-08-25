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
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use App\Models\Category;

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
                            ->label('Descripción')
                            ->required()
                            ->columnSpanFull()
                            //->fileAttachmentsDirectory('products')
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                                'table',
                            ]),
                    ])->columns(2),
                    
                    Forms\Components\Section::make("Imágenes del producto")->schema([
                        Forms\Components\FileUpload::make('images')
                            ->label('Imágenes')
                            ->multiple()
                            ->directory('product')
                            ->maxFiles(10)
                            ->reorderable()
                            ->uploadingMessage('Subiendo imágenes...')
                            
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
                            ->label('Categoría del producto')
                            ->relationship('category', 'name')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                ->label('Nombre')
                                ->required()
                                ->maxLength(255)
                                ->live()
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                Forms\Components\TextInput::make('slug')
                                ->label('Nombre Corto')
                                ->required()
                                ->readOnly()
                                ->maxLength(255)
                                ->dehydrated(),
                            ])
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
                    Forms\Components\Section::make('Stocks')
                    ->relationship('stock')
                    ->schema([

                        Forms\Components\TextInput::make('stock_quantity_real')
                        ->numeric()
                        ->label('Stock Real')
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set, ?int $state) => $set('stock_quantity_virtual', $state))
                        ->afterStateUpdated(function (Set $set, ?int $state) {
                            if ($state === 0) {
                                $set('stock_available', false);
                            } else {
                                $set('stock_available', true);
                            }
                        })
                        ->hintIcon('heroicon-o-exclamation-circle', tooltip: 'El stock real es aquel que determina la cantidad de unidades de un producto.')
                        ->hintColor('primary'),

                        Forms\Components\TextInput::make('stock_quantity_virtual')
                        ->numeric()
                        ->label('Stock Virtual')
                        ->required()
                        ->dehydrated()
                        ->readOnly()
                        ->hintIcon('heroicon-o-exclamation-circle', tooltip: 'El stock virtual es aquel que visualiza el usuario.')
                        ->hintColor('primary'),

                        Forms\Components\Toggle::make('stock_available')
                            ->label('Está Disponible?')
                            ->required()
                            ->disabled()
                            ->default(true)
                    ]),
                ])->columnSpan(1)

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->label('Color'),
                Tables\Columns\TextColumn::make('brand')
                    ->label('Marca')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Precio Mayorista')
                    ->money()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('suggest_price')
                    ->label('Precio Sugerido')
                    ->money()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Es Destacado?')
                    ->boolean(),
                Tables\Columns\IconColumn::make('on_sale')
                    ->label('En Oferta?')
                    ->boolean(),
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
                Tables\Actions\ViewAction::make()
                ->label('Ver')
                ->modalHeading('Vista de Producto'),
                Tables\Actions\EditAction::make()
                ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                ->label('')
                ->modalHeading('Borrar Producto'),
            ]);
            /*->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);*/
            /*
            ->recordUrl(
                fn (Product $record): string => Pages\ViewProduct::getUrl([$record->id]),
            );*/
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TagsRelationManager::class,
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
    public static function getBreadcrumb(): string
    {
        return 'Productos';
    }
}
