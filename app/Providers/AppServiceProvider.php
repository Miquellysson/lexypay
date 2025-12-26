<?php

namespace App\Providers;

use App\Payments\Stripe\StripeWebhookVerifier;
use App\Payments\Stripe\WebhookVerifier;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WebhookVerifier::class, StripeWebhookVerifier::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
