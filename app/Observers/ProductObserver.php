<?php

namespace App\Observers;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class ProductObserver
{
    public function created(Product $product): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('New Product Added')
            ->body("Product: {$product->name}\nPrice: {$product->price}")
            ->icon('heroicon-o-shopping-bag')
            ->actions([
                Action::make('view')
                    ->button()
                    ->url(ProductResource::getUrl('view', ['record' => $product])),
            ])
            ->sendToDatabase($recipient);
    }

    public function deleted(Product $product): void
    {
        $recipient = auth()->user();

        Notification::make()
            ->title('Product Deleted')
            ->body("Product {$product->name} has been removed")
            ->icon('heroicon-o-trash')
            ->danger()
            ->sendToDatabase($recipient);
    }
}
