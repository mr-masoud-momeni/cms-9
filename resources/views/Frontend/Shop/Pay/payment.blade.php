@extends('Frontend.Store.Layouts.MasterMain')

@section('header')
    @include('Frontend.Store.Layouts.header-minimal')
@endsection

@section('page-styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/store-payment.css') }}">
@endsection

@section('Main')

    <main class="store-payment-page">

        @include('Frontend.layouts.errors')
        @include('Frontend.layouts.message')

        <div class="store-payment-card">

            <div class="store-payment-heading">

                <div class="store-payment-icon">
                    <i class="bi bi-credit-card"></i>
                </div>

                <div>
                    <h1>انتخاب روش پرداخت</h1>
                    <p>روش پرداخت سفارش خود را انتخاب کنید.</p>
                </div>

            </div>


            <div class="store-payment-amount">

                <span>مبلغ قابل پرداخت</span>

                <strong>
                    {{ number_format($totalAmount) }}
                    <small>تومان</small>
                </strong>

            </div>


            <div class="store-payment-methods">

                @if($gateway)

                    <form
                        method="POST"
                        action="{{ route('payment.online') }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="store-payment-method store-payment-online"
                        >
                            <span class="store-payment-method-icon">
                                <i class="bi bi-credit-card"></i>
                            </span>

                            <span class="store-payment-method-info">
                                <strong>پرداخت آنلاین</strong>
                                <small>پرداخت امن از طریق درگاه بانکی</small>
                            </span>

                            <i class="bi bi-chevron-left store-payment-arrow"></i>
                        </button>
                    </form>

                @endif


                @if($bankAccount)

                    <a
                        href="{{ route('payment.card_to_card') }}"
                        class="store-payment-method"
                    >
                        <span class="store-payment-method-icon">
                            <i class="bi bi-bank"></i>
                        </span>

                        <span class="store-payment-method-info">
                            <strong>پرداخت کارت به کارت</strong>
                            <small>واریز مستقیم به حساب فروشگاه</small>
                        </span>

                        <i class="bi bi-chevron-left store-payment-arrow"></i>
                    </a>

                @endif


                @if(!$gateway && !$bankAccount)

                    <div class="store-payment-empty">

                        <i class="bi bi-exclamation-circle"></i>

                        <div>
                            <strong>روش پرداختی فعال نیست</strong>

                            <p>
                                در حال حاضر هیچ روش پرداختی برای این فروشگاه فعال نیست.
                            </p>
                        </div>

                    </div>

                @endif

            </div>

        </div>

    </main>

@endsection