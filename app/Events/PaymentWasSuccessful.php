<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentWasSuccessful
{
    use Dispatchable, SerializesModels;

    public $payment;

    public function __construct(Order $order)
    {
        $this->payment = $order->payment;
    }
}
