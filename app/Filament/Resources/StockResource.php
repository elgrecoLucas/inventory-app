<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;
    protected static ?string $navigationGroup = 'Gestión de productos';
    protected static ?int $navigationSort = 3;
    //protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function form(Form $form): Form
    {   
        return $form
            ->schema([
                Forms\Components\TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required(),
                Forms\Components\TextInput::make('stock_quantity_virtual')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('stock_quantity_real')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('stock_available')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.category.name')
                    ->label('Categoria')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Producto')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.color')
                    ->label('Color')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity_virtual')
                    ->label('Stock Virtual')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_quantity_real')
                    ->label('Stock Real')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('stock_available')
                    ->label('Stock Disponible')    
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
            ]);
            /*->actions([
                Tables\Actions\EditAction::make(),
            ])
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
            'index' => Pages\ListStocks::route('/'),
            //'create' => Pages\CreateStock::route('/create'),
            //'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
