<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\User;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use NumberFormatter;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class Checkout extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.resources.product-resource.pages.checkout';

    protected StripeClient $stripe;

    protected string $checkoutKey;

    public string $clientSecret;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function mount(int|string $record)
    {
        // TODO: page content
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);

        $this->record = $this->resolveRecord($record);
        $this->heading = 'Checkout: '.$this->record->name;
        $this->subheading = $formatter->formatCurrency($this->record->price / 100, 'eur');
        // TODO: separate checkout
        $this->checkoutKey = 'checkout '.$this->record->id;
        $this->clientSecret = $this->getClientSecret();
    }

    protected function getStripeCustomer(User $user)
    {
        if ($user->stripe_customer_id !== null) {
            return $this->stripe->customers->retrieve($user->stripe_customer_id);
        }

        $customer = $this->stripe->customers->create([
            'name' => $user->name,
            'email' => $user->email,
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);

        return $customer;
    }

    protected function getPaymentIntent(Customer $customer): PaymentIntent
    {
        $paymentIntentId = session($this->checkoutKey);

        if ($paymentIntentId === null) {
            return $this->createNewPaymentIntent($customer);
        }

        $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);

        if ($paymentIntent->status !== 'requires_payment_method') {
            return $this->createNewPaymentIntent($customer);
        }

        return $paymentIntent;
    }

    protected function getClientSecret(): string
    {
        $user = auth()->user();
        $customer = $this->getStripeCustomer($user);
        $paymentIntent = $this->getPaymentIntent($customer);

        return $paymentIntent->client_secret;
    }

    protected function createNewPaymentIntent(Customer $customer): PaymentIntent
    {
        $paymentIntent = $this->stripe->paymentIntents->create([
            'customer' => $customer->id,
            'setup_future_usage' => 'off_session',
            'amount' => $this->record->price,
            'currency' => config('services.stripe.currency'),
            'metadata' => [
                'product_id' => $this->record->id,
                'user_id' => auth()->id(),
            ],
        ]);

        session([$this->checkoutKey => $paymentIntent->id]);

        return $paymentIntent;
    }
}
