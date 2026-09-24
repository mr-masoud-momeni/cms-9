@extends('Customer.layouts.Master')

@section('content')
    <div class="row">
        <div class="col-lg-12">

            @include('Customer.layouts.errors')

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>تنظیمات فروشگاه</h3>
                </div>

                <div class="panel-body">
                    <form action="{{ route('shop.settings.update') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="shipping_cost">هزینه ثابت ارسال (تومان)</label>
                            <input
                                type="number"
                                id="shipping_cost"
                                name="shipping_cost"
                                class="form-control"
                                min="0"
                                step="1"
                                value="{{ old('shipping_cost', $shop->shipping_cost ?? 0) }}"
                                required
                            >
                            <p class="help-block">
                                این مبلغ در زمان ثبت سفارش ذخیره می‌شود و در مبلغ نهایی سفارش محاسبه خواهد شد.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            ذخیره تنظیمات
                        </button>
                    </form>
                </div>

                <div class="panel-footer">
                    تنظیمات فروشگاه
                </div>
            </div>

        </div>
    </div>
@endsection
