<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferResource\Pages;
use App\Models\Transfer;
use App\Models\Store;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransferResource extends Resource
{
    protected static ?string $model = Transfer::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Transfer Details')
                ->schema([
                    Forms\Components\TextInput::make('transfer_number')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->default(fn () => Transfer::generateTransferNumber()),
                    Forms\Components\Select::make('from_store_id')
                        ->relationship('fromStore', 'name')
                        ->required(),
                    Forms\Components\Select::make('to_store_id')
                        ->relationship('toStore', 'name')
                        ->required()
                        ->different('from_store_id'),
                    Forms\Components\Select::make('product_id')
                        ->relationship('product', 'name')
                        ->searchable()
                        ->required(),
                ])->columns(2),
            
            Forms\Components\Section::make('Quantities & Status')
                ->schema([
                    Forms\Components\TextInput::make('quantity_requested')
                        ->numeric()
                        ->required()
                        ->minValue(1),
                    Forms\Components\TextInput::make('quantity_shipped')
                        ->numeric()
                        ->minValue(0),
                    Forms\Components\TextInput::make('quantity_received')
                        ->numeric()
                        ->minValue(0),
                    Forms\Components\Select::make('status')
                        ->options([
                            'requested' => 'Requested',
                            'approved' => 'Approved',
                            'shipped' => 'Shipped',
                            'received' => 'Received',
                            'cancelled' => 'Cancelled'
                        ])
                        ->default('requested')
                        ->required(),
                ])->columns(4),
            
            Forms\Components\Section::make('Dates')
                ->schema([
                    Forms\Components\DatePicker::make('requested_date')
                        ->required()
                        ->default(today()),
                    Forms\Components\DatePicker::make('shipped_date'),
                    Forms\Components\DatePicker::make('received_date'),
                ])->columns(3),
            
            Forms\Components\Section::make('Additional Information')
                ->schema([
                    Forms\Components\Textarea::make('reason')->rows(2),
                    Forms\Components\Textarea::make('notes')->rows(2),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transfer_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fromStore.name')
                    ->label('From')
                    ->limit(15),
                Tables\Columns\TextColumn::make('toStore.name')
                    ->label('To')
                    ->limit(15),
                Tables\Columns\TextColumn::make('product.name')
                    ->limit(20),
                Tables\Columns\TextColumn::make('quantity_requested')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('quantity_shipped')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('quantity_received')
                    ->alignCenter(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'requested',
                        'warning' => 'approved',
                        'primary' => 'shipped',
                        'success' => 'received',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('requested_date')
                    ->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('from_store_id')
                    ->relationship('fromStore', 'name')
                    ->label('From Store'),
                Tables\Filters\SelectFilter::make('to_store_id')
                    ->relationship('toStore', 'name')
                    ->label('To Store'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Transfer $record) => $record->status === 'requested')
                    ->action(fn (Transfer $record) => $record->approve(auth()->user())),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfer::route('/create'),
            'edit' => Pages\EditTransfer::route('/{record}/edit'),
        ];
    }
}