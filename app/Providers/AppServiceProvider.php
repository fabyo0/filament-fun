<?php

declare(strict_types=1);

namespace App\Providers;

use BezhanSalleh\FilamentLanguageSwitch\Enums\Placement;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            $this->app->isProduction()
        );
    }

    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function configurePasswordValidation(): void
    {
        Password::defaults(fn () => $this->app->isProduction() ? Password::min(8)->uncompromised() : null);
    }

    private function configureUrl(): void
    {
        URL::forceHttps(App::isProduction());
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
        Model::unguard();
    }

    private function languageSwitch(): void
    {
        // Switch language
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'tr'])
                ->circular()
                ->outsidePanelPlacement(Placement::BottomRight)
                ->visible(outsidePanels: true);
        });
    }

    private function filamentAsset(): void
    {
        FilamentAsset::register([
            Js::make('stripe-js', 'https://js.stripe.com/v3/'),
        ]);
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureModels();
        $this->configureUrl();
        $this->configureVite();
        $this->configureCommands();
        $this->configurePasswordValidation();
//        $this->languageSwitch();
        $this->filamentAsset();
    }
}
