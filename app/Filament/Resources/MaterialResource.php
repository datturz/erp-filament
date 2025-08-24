<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Material Details')
                ->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('sku')->required()->unique(ignoreRecord: true),
                    Forms\Components\Select::make('type')
                        ->options(['fabric' => 'Fabric', 'button' => 'Button', 'zipper' => 'Zipper', 'thread' => 'Thread', 'label' => 'Label', 'other' => 'Other'])
                        ->required(),
                    Forms\Components\Textarea::make('description')->rows(2),
                ])->columns(2),
            
            Forms\Components\Section::make('Specifications')
                ->schema([
                    Forms\Components\TextInput::make('color'),
                    Forms\Components\TextInput::make('size'),
                    Forms\Components\Select::make('unit_of_measure')
                        ->options(['yards' => 'Yards', 'pieces' => 'Pieces', 'meters' => 'Meters', 'rolls' => 'Rolls', 'boxes' => 'Boxes'])
                        ->required(),
                    Forms\Components\TextInput::make('unit_cost')->numeric()->prefix('$')->required(),
                ])->columns(2),
            
            Forms\Components\Section::make('Stock & Supplier')
                ->schema([
                    Forms\Components\TextInput::make('current_stock')->numeric()->default(0),
                    Forms\Components\TextInput::make('minimum_stock')->numeric()->default(0),
                    Forms\Components\TextInput::make('maximum_stock')->numeric(),
                    Forms\Components\TextInput::make('supplier_name'),
                    Forms\Components\TextInput::make('supplier_sku'),
                    Forms\Components\TextInput::make('lead_time_days')->numeric()->default(7),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->limit(25),
                Tables\Columns\BadgeColumn::make('type')->colors(['primary']),
                Tables\Columns\TextColumn::make('color')->badge(),
                Tables\Columns\TextColumn::make('unit_cost')->money('USD')->alignEnd(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->alignCenter()
                    ->color(fn ($record) => $record->isLowStock() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('supplier_name')->limit(20),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type'),
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn ($query) => $query->whereRaw('current_stock <= minimum_stock')),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}