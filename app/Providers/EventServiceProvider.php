<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Authenticated;
use App\Listeners\MergeCartAfterLogin;
use App\Events\PaymentWasSuccessful;
use App\Events\CardToCardPaymentSubmitted;
use App\Listeners\SendPaymentSuccessToBale;
use App\Listeners\SendCardToCardPaymentToBale;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Authenticated::class => [
            MergeCartAfterLogin::class,
        ],
        PaymentWasSuccessful::class => [
            SendPaymentSuccessToBale::class,
        ],
        CardToCardPaymentSubmitted::class => [
            SendCardToCardPaymentToBale::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
