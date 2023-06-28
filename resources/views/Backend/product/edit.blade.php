@extends('Backend.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Backend.layouts.errors')
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ویرایش محصول</h3></div>
                <div class="panel-body">


                    <div class="row">
                        <form action="{{route('product.update',['product'=>$product->slug])}}" method="post" enctype="multipart/form-data">
                            <div class="col-md-8">
                                {!! csrf_field() !!}
                                {{method_field('patch')}}
                                <div class="form-group">
                                    <label for="title">عنوان محصول</label>
                                    <input type="text" name="title" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{$product->title}}">
                                </div>

                                <div class="form-group">
                                    <label for="body">متن محصول</label>
                                    <textarea name="body" class="form-control" cols="30" id="body" rows="10" placeholder="متن را وارد کنید.">{{$product->body}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="type">نوع محصول</label>
                                    <div class="radio">
                                        <label><input type="radio" name="type" value="physical" @php if(!$product->link){ echo 'checked';} @endphp>محصول فیزیکی</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="type" value="virtual" @php if($product->link){ echo 'checked';} @endphp>محصول مجازی</label>
                                    </div>
                                </div>
                                <div class="form-group" id="link" style="display:none;">
                                    <label for="link">لینک محصول</label>
                                    <input  type="text" name="link" class="form-control">
                                </div>
                                <div class="form-group" id="product-body-display" style="display:none;">
                                    <label for="product-body">متن محصول</label>
                                    <textarea name="product-body" class="form-control" cols="30" id="product-body" rows="10" placeholder="متن را وارد کنید.">{{old('product-body')}}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" id="price-type">
                                    <label for="price-type">نوع دسترسی</label>
                                    <div class="radio">
                                        <label><input type="radio" name="price-type" value="non-membership" checked>بدون عضویت</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="price-type" value="membership">عضویت</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="price-type" value="special-membership">اعضای ویژه</label>
                                    </div>
                                    <div class="radio">
                                        <label><input type="radio" name="price-type" value="cash">نقدی</label>
                                    </div>
                                </div>
                                <div class="form-group" id="price" style="display: none;" >
                                    <label for="price">قیمت محصول</label>
                                    <input  type="text" name="price" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="images">تصویر شاخص</label>
                                    <input type="file" name="images"  id="images"  >
                                </div>
                                <div class="form-group">
                                    <label for="category">دسته بندی ها : </label>
                                    <div class="ShowCategorySelect">
                                        @foreach($parentCategories as $category)
                                            <ul>
                                                <li>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="category[]" value="{{$category->id}}" >
                                                            <input type="checkbox" name="category[]" value="{{$category->id}}" >
                                                            {{$category->name}}
                                                        </label>
                                                        @if(count($category->subcategory))
                                                            @include('Backend.product.subCategoryList',['subcategories' => $category->subcategory])
                                                        @endif
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">افزودن محصول</button>
                            </div>
                        </form>
                    </div>


                </div>
                <div class="panel-footer">Panel Footer</div>
            </div>

        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{asset('/ckeditor/ckeditor.js')}}"></script>
    <script>
        CKEDITOR.replace('body',{
            filebrowserUploadUrl: '{{route('uploadImage')}}',
            filebrowserImageUploadUrl: '{{route('uploadImage')}}'
        });
        CKEDITOR.replace('product-body',{
            filebrowserUploadUrl: '{{route('uploadImage')}}',
            filebrowserImageUploadUrl: '{{route('uploadImage')}}'
        });
    </script>
    <script>
        $(document).ready(function() {
            $("input[type='radio']").change(function() {
                if ($(this).val() == "virtual") {
                    $("#link").show();
                    $("#product-body-display").show();
                } else {
                    $("#link").hide();
                    $("#product-body-display").hide();
                }
            });
            var radioValue = $("input[name='type']:checked").val();
            if(radioValue == 'virtual'){
                $("#link").show();
                $("#product-body-display").show();
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#price-type input[type='radio']").change(function() {
                if ($(this).val() == "cash") {
                    $("#price").show();
                } else {
                    $("#price").hide();
                }
            });
        });
    </script>
@endsection
