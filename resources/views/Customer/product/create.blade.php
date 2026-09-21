@extends('Customer.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Customer.layouts.errors')
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ایجاد محصول</h3></div>
                <div class="panel-body">
                    <div class="row">
                        <form action="{{route('shop.product.store')}}" method="post" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">عنوان محصول</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                           placeholder="مثلاً: پارچه لینن" value="{{old('title')}}" required>
                                </div>

                                <div class="form-group">
                                    <label for="body">توضیحات</label>
                                    <textarea name="body" class="form-control" id="body" rows="8"
                                              placeholder="توضیحات محصول را بنویسید..." required>{{old('body')}}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">قیمت</label>
                                    <input type="number" name="price" class="form-control" id="price"
                                           min="0" step="any" placeholder="مثلاً 250000" value="{{old('price')}}" required>
                                </div>

                                <div class="form-group">
                                    <label for="unit">واحد فروش</label>
                                    <select name="unit" id="unit" class="form-control" required>
                                        @php($units = ['عدد', 'متر', 'سانتی‌متر', 'کیلوگرم', 'گرم', 'لیتر'])
                                        @foreach($units as $unit)
                                            <option value="{{$unit}}" @selected(old('unit', 'عدد') === $unit)>{{$unit}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">قیمت بر اساس هر واحد محاسبه می‌شود.</small>
                                </div>

                                <div class="form-group">
                                    <label for="images">تصویر محصول</label>
                                    <input type="file" name="images" id="images" class="form-control"
                                           accept="image/jpeg,image/png,image/webp" required>
                                    <small class="text-muted">یک تصویر انتخاب کنید.</small>
                                </div>

                                <button type="submit" class="btn btn-success btn-block">افزودن محصول</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
