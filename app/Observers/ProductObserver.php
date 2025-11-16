<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;

class ProductObserver
{
    public function created(Product $product): void
    {
        $this->notifyAdmins(
            'New Product Created',
            "Product {$product->name} has been created successfully",
            'heroicon-o-shopping-bag'
        );
    }

    public function updated(Product $product): void
    {
        $this->notifyAdmins(
            'Product Updated',
            "Product {$product->name} has been updated",
            'heroicon-o-pencil'
        );
    }

    public function deleted(Product $product): void
    {
        $this->notifyAdmins(
            'Product Deleted',
            "Product {$product->name} has been removed",
            'heroicon-o-trash',
            true // danger
        );
    }

    protected function notifyAdmins(string $title, string $body, string $icon, bool $danger = false): void
    {
        $admins = User::role('admin')->get();

        if ($admins->isEmpty()) {
            return;
        }

        $notification = Notification::make()
            ->title($title)
            ->body($body)
            ->icon($icon);

        if ($danger) {
            $notification->danger();
        }

        $notification->sendToDatabase($admins);
    }
}
