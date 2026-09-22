<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_PENDING   = 'pending';
    const STATUS_RESERVED  = 'reserved';
    const STATUS_PAID      = 'paid';
    const STATUS_SHIPPED   = 'shipped';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const RESERVATION_MINUTES = 5;

    const MESSAGE_RESERVATION_CREATED = 'موجودی برای شما رزرو شد. لطفاً حداکثر تا :minutes دقیقه پرداخت خود را انجام دهید؛ در غیر این صورت رزرو شما به‌صورت خودکار آزاد خواهد شد.';
    const MESSAGE_RESERVATION_CARD_TO_CARD = 'موجودی برای شما رزرو شد. لطفاً حداکثر تا :minutes دقیقه واریز را انجام داده و رسید پرداخت را ثبت کنید. پس از این زمان، رزرو شما آزاد خواهد شد.';
    const MESSAGE_STOCK_CONFLICT = 'این محصول در حال حاضر توسط مشتری دیگری رزرو شده است. فقط :available :unit قابل رزرو است.';
    const MESSAGE_OUT_OF_STOCK = 'این محصول در حال حاضر ناموجود است.';
    const MESSAGE_PRODUCT_UNAVAILABLE = 'یکی از محصولات سبد خرید دیگر قابل سفارش نیست.';
    const MESSAGE_STOCK_COMMIT_FAILED = 'موجودی محصول هنگام نهایی‌سازی سفارش کافی نیست.';

    protected $casts = [
        'paid_at' => 'datetime',
        'reserved_at' => 'datetime',
        'reservation_expires_at' => 'datetime',
    ];

    public function isReservationExpired(): bool
    {
        if ($this->status !== self::STATUS_RESERVED || !$this->reservation_expires_at) {
            return false;
        }

        if ($this->payment && $this->payment->status === 'waiting_confirmation') {
            return false;
        }

        return $this->reservation_expires_at->isPast();
    }

    public function reservationMessage(bool $cardToCard = false): string
    {
        $message = $cardToCard
            ? self::MESSAGE_RESERVATION_CARD_TO_CARD
            : self::MESSAGE_RESERVATION_CREATED;

        return str_replace(':minutes', (string) self::RESERVATION_MINUTES, $message);
    }

    protected $fillable = [
        'buyer_id',
        'shop_id',
        'status',
        'total',
        'paid_at',
        'reserved_at',
        'reservation_expires_at',
        'tracking_code',
        'receiver_name',
        'receiver_phone',
        'receiver_province',
        'receiver_city',
        'receiver_postal_code',
        'receiver_address',
    ];

    public function buyer()
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity', 'price');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class); // یک سفارش یک پرداخت نهایی دارد
    }
}
