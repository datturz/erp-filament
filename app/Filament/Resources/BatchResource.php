<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BatchResource\Pages;
use App\Models\Batch;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Production';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Batch Information')
                ->schema([
                    Forms\Components\TextInput::make('batch_number')->required()->unique(ignoreRecord: true),
                    Forms\Components\Select::make('product_id')
                        ->relationship('product', 'name')
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => 
                            $state ? $set('batch_number', Batch::generateBatchNumber($state)) : null
                        ),
                    Forms\Components\Select::make('status')
                        ->options(['planned' => 'Planned', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])
                        ->default('planned')
                        ->required(),
                ])->columns(3),
            
            Forms\Components\Section::make('Production Planning')
                ->schema([
                    Forms\Components\TextInput::make('quantity_planned')->numeric()->required()->minValue(1),
                    Forms\Components\TextInput::make('quantity_produced')->numeric()->default(0)->minValue(0),
                    Forms\Components\TextInput::make('quantity_quality_passed')->numeric()->default(0)->minValue(0),
                    Forms\Components\TextInput::make('quantity_defective')->numeric()->default(0)->minValue(0),
                ])->columns(4),
            
            Forms\Components\Section::make('Schedule')
                ->schema([
                    Forms\Components\DatePicker::make('planned_start_date')->required(),
                    Forms\Components\DatePicker::make('planned_end_date')->required(),
                    Forms\Components\DateTimePicker::make('actual_start_date'),
                    Forms\Components\DateTimePicker::make('actual_end_date'),
                ])->columns(2),
            
            Forms\Components\Section::make('Cost Tracking')
                ->schema([
                    Forms\Components\TextInput::make('estimated_cost')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('actual_cost')->numeric()->prefix('$')->readOnly(),
                ])->columns(2),
            
            Forms\Components\Textarea::make('notes')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('batch_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('product.name')->limit(20),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'planned',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('quantity_planned')->alignCenter(),
                Tables\Columns\TextColumn::make('quantity_produced')->alignCenter(),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->formatStateUsing(fn ($state) => round($state, 1) . '%')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('planned_end_date')->date(),
                Tables\Columns\TextColumn::make('actual_cost')->money('USD')->alignEnd(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn ($query) => $query->where('status', 'in_progress')->where('planned_end_date', '<', now())),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'edit' => Pages\EditBatch::route('/{record}/edit'),
        ];
    }
}