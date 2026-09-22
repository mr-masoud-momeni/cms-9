@extends('Customer.layouts.Master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if($message = session('shop_updated'))
                <div class="alert alert-success">{{ $message }}</div>
            @endif

            @include('Backend.layouts.errors')

            <div class="panel panel-default">
                <div class="panel-heading">مشخصات فروشگاه</div>
                <div class="panel-body">
                    <form action="{{ route('shop.settings.update') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name">نام فروشگاه</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $shop->name) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="description">توضیحات زیر نام فروشگاه</label>
                            <textarea name="description" id="description" class="form-control" rows="4" maxlength="1000">{{ old('description', $shop->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="logo">لوگوی فروشگاه</label>
                            @if($shop->logo)
                                <div style="margin-bottom:10px;">
                                    <img src="{{ asset($shop->logo) }}" alt="{{ $shop->name }}" style="max-width:120px;max-height:120px;">
                                </div>
                            @endif
                            <input type="file" name="logo" id="logo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="help-block">فرمت‌های JPG، PNG و WebP — حداکثر ۲ مگابایت</small>
                        </div>

                        <button type="submit" class="btn btn-success">ذخیره تغییرات</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
