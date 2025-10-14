<?php

namespace App\Listeners;

use App\Events\PaymentWasSuccessful;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentSuccessEmails
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PaymentWasSuccessful  $event
     * @return void
     */
    public function handle(PaymentWasSuccessful $event)
    {
        $payment = $event->payment;

        // ارسال ایمیل به مشتری
        if ($payment->order && $payment->order->buyer && $payment->buyer->email) {
            Mail::to($payment->buyer->email)
                ->send(new PaymentSuccessCustomer($payment));
        }

        // ارسال ایمیل به مدیر فروشگاه
        if ($payment->shop && $payment->shop->user && $payment->shop->user->email) {
            Mail::to($payment->shop->user->email)
                ->send(new PaymentSuccessAdmin($payment));
        }
    }
}
