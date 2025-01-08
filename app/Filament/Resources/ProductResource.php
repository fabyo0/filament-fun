<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use NumberFormatter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make('Product Information')
                            ->description('Provide the details of the product below.')
                            ->icon('heroicon-o-cube')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Product Name')
                                            ->required()
                                            ->placeholder('Enter product name')
                                            ->maxLength(255)
                                            ->helperText('Enter the official name of the product.')
                                            ->prefixIcon('heroicon-o-archive-box'),

                                        Forms\Components\TextInput::make('price')
                                            ->label('Price')
                                            ->alphaNum()
                                            ->minValue(0)
                                            ->required()
                                            ->placeholder('Enter product price')
                                            ->numeric()
                                            ->helperText('Enter the price of the product.')
                                            ->prefixIcon('heroicon-o-currency-dollar'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->color('primary')
                    ->icon('heroicon-o-cube')
                    ->weight('bold')
                    ->toggleable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->money('eur')
                    ->alignEnd()
                    ->color('success')
                    ->icon('heroicon-o-currency-euro')
                    ->toggleable()
                    ->summarize([
                        Summarizer::make()
                            ->label('Average')
                            ->using(fn ($query) => $query->avg('price') / 100)
                            ->money('eur'),
                    ]),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                Filter::make('price_range')
                    ->form([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('price_from')
                                    ->numeric()
                                    ->label('Minimum Price'),
                                Forms\Components\TextInput::make('price_to')
                                    ->numeric()
                                    ->label('Maximum Price'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price * 100),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price * 100),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Product Information')
                ->description('Detailed product information and pricing')
                ->icon('heroicon-o-information-circle')
                ->collapsible()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextEntry::make('name')
                                ->label('Product Name')
                                ->weight('bold')
                                ->columnSpan(1)
                                ->icon('heroicon-o-cube')
                                ->copyable()
                                ->color('blue-600'),

                            TextEntry::make('price')
                                ->label('Price')
                                ->formatStateUsing(function ($state) {
                                    $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);

                                    return $formatter->formatCurrency(amount: $state / 100, currency: 'eur');
                                })
                                ->columnSpan(1)
                                ->icon('heroicon-o-currency-euro')
                                ->color('green-600')
                                ->badge(),

                            TextEntry::make('created_at')
                                ->label('Listed Date')
                                ->dateTime()
                                ->columnSpan(1)
                                ->icon('heroicon-o-calendar'),
                        ]),

                    Actions::make([
                        Action::make('Buy product')
                            ->url(fn ($record): string => self::getUrl('checkout', [$record]))
                            ->icon('heroicon-o-shopping-cart')
                            ->size('lg')
                            ->color('success')
                            ->iconPosition('left')
                            ->tooltip('Proceed to checkout'),
                    ])
                        ->alignment('left'),
                ])
                ->columns(1),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here as needed
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Product::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'checkout' => Pages\Checkout::route('/{record}/checkout'),
        ];
    }
}
