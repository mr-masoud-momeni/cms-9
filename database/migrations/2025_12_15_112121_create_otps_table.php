<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            // پلی‌مورفیک
            $table->morphs('otpable');

            // منظور OTP: login, reset_password, verify_mobile
            $table->string('purpose');

            // کد هش‌شده OTP
            $table->string('code_hash');

            // زمان انقضا
            $table->timestamp('expires_at');

            // شمارش تلاش‌ها
            $table->unsignedTinyInteger('attempts')->default(0);

            // بلاک موقت
            $table->timestamp('blocked_until')->nullable();

            $table->timestamps();

            // ایندکس برای جستجوی سریع
            $table->index(['otpable_id','otpable_type','purpose']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otps');
    }
}
