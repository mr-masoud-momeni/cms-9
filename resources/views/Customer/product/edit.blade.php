@extends('Customer.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Customer.layouts.errors')
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ویرایش محصول</h3></div>
                <div class="panel-body">
                    <div class="row">
                        <form action="{{route('shop.product.update',['product'=>$product->slug])}}" method="post" enctype="multipart/form-data">
                            {!! csrf_field() !!}
                            {{method_field('patch')}}

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">عنوان محصول</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                           value="{{old('title', $product->title)}}" required>
                                </div>

                                <div class="form-group">
                                    <label for="body">توضیحات</label>
                                    @php
                                        $body = old('body', $product->body);
                                        $body = preg_replace('/<br\s*\/?>(?=\s*)/i', "\n", $body);
                                        $body = preg_replace('/<\/(p|div|li|h[1-6])>/i', "\n", $body);
                                        $body = trim(strip_tags(html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
                                    @endphp
                                    <textarea name="body" class="form-control" id="body" rows="8"
                                              placeholder="توضیحات محصول را بنویسید..." required>{{ $body }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">قیمت</label>
                                    <input type="number" name="price" class="form-control" id="price"
                                           min="0" step="any" value="{{old('price', $product->price)}}" required>
                                </div>

                                <div class="form-group">
                                    <label for="unit">واحد فروش</label>
                                    @php
                                        $units = ['عدد', 'متر', 'سانتی‌متر', 'کیلوگرم', 'گرم', 'لیتر'];
                                        $selectedUnit = old('unit') ?: ($product->unit ?: 'عدد');
                                        if (!in_array($selectedUnit, $units, true)) {
                                            $selectedUnit = 'عدد';
                                        }
                                    @endphp
                                    <select name="unit" id="unit" class="form-control" required>
                                        @foreach($units as $unit)
                                            <option value="{{$unit}}" {{ $selectedUnit === $unit ? "selected" : "" }}>{{$unit}}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">قیمت بر اساس هر واحد محاسبه می‌شود.</small>
                                </div>

                                <div class="form-group">
                                    <label for="stock">موجودی</label>
                                    <input type="number" name="stock" class="form-control" id="stock"
                                           min="0" step="1" value="{{old('stock', $product->stock)}}" required>
                                    <small class="text-muted">مقدار موجودی بر اساس واحد فروش بالا ثبت می‌شود.</small>
                                </div>

                                <div class="form-group">
                                    <label for="images">تصویر محصول</label>

                                    @php
                                        $currentImage = is_array($product->images)
                                            ? ($product->images['original'] ?? $product->images['thum'] ?? null)
                                            : $product->images;
                                    @endphp

                                    @if($currentImage)
                                        <div style="margin-bottom: 12px;">
                                            <img src="{{asset(ltrim($currentImage, '/'))}}"
                                                 alt="{{ $product->title }}"
                                                 style="display:block;width:120px;height:120px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                        </div>
                                    @endif

                                    <input type="file" name="images" id="images" class="form-control"
                                           accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">برای جایگزینی تصویر، فایل جدید را انتخاب کنید.</small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">ذخیره تغییرات</button>
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
