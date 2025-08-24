<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Product Details')
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('sku')->required()->unique(ignoreRecord: true),
                    Forms\Components\Select::make('type')
                        ->options(['jeans' => 'Jeans', 'chinos' => 'Chinos', 'dress_pants' => 'Dress Pants', 'shorts' => 'Shorts'])
                        ->required(),
                    Forms\Components\Textarea::make('description')->rows(3),
                ])->columns(2),
            
            Forms\Components\Section::make('Specifications')
                ->schema([
                    Forms\Components\Select::make('size')
                        ->options(['XS' => 'XS', 'S' => 'S', 'M' => 'M', 'L' => 'L', 'XL' => 'XL', 'XXL' => 'XXL', '28' => '28', '30' => '30', '32' => '32', '34' => '34', '36' => '36', '38' => '38', '40' => '40', '42' => '42'])
                        ->required(),
                    Forms\Components\TextInput::make('color')->required(),
                    Forms\Components\Select::make('fit')
                        ->options(['slim' => 'Slim', 'regular' => 'Regular', 'relaxed' => 'Relaxed', 'bootcut' => 'Bootcut', 'straight' => 'Straight'])
                        ->required(),
                    Forms\Components\TextInput::make('barcode')->unique(ignoreRecord: true),
                ])->columns(2),
            
            Forms\Components\Section::make('Pricing & Stock')
                ->schema([
                    Forms\Components\TextInput::make('retail_price')->numeric()->prefix('$')->required(),
                    Forms\Components\TextInput::make('wholesale_price')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('cost_price')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('current_stock')->numeric()->default(0),
                    Forms\Components\TextInput::make('minimum_stock')->numeric()->default(0),
                    Forms\Components\Toggle::make('is_active')->default(true),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->limit(30),
                Tables\Columns\BadgeColumn::make('type')->colors(['primary']),
                Tables\Columns\TextColumn::make('color')->badge(),
                Tables\Columns\TextColumn::make('size')->alignCenter(),
                Tables\Columns\TextColumn::make('retail_price')->money('USD')->alignEnd(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->alignCenter()
                    ->color(fn ($record) => $record->isLowStock() ? 'danger' : 'success'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type'),
                Tables\Filters\SelectFilter::make('size'),
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn ($query) => $query->whereRaw('current_stock <= minimum_stock')),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}