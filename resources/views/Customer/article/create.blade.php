@extends('Customer.layouts.Master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Customer.layouts.errors')

            <div class="panel panel-default">
                <div class="panel-heading"><h3>ارسال مقاله</h3></div>
                <div class="panel-body">
                    <form action="{{ route('shop.article.store') }}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">عنوان مقاله</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                           value="{{ old('title') }}" placeholder="عنوان را وارد کنید...">
                                </div>

                                <div class="form-group">
                                    <label for="body">متن مقاله</label>
                                    <textarea name="body" class="form-control" id="body" rows="12"
                                              placeholder="متن مقاله را وارد کنید...">{{ old('body') }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="images">تصویر شاخص</label>
                                    <input type="file" name="images" id="images">
                                </div>

                                <div class="form-group">
                                    <label>دسته‌بندی‌ها</label>
                                    <div class="ShowCategorySelect">
                                        @foreach($parentCategories as $category)
                                            <ul>
                                                <li>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="category[]" value="{{ $category->id }}">
                                                            {{ $category->name }}
                                                        </label>

                                                        @if(count($category->subcategory))
                                                            @include('Customer.article.subCategoryList', ['subcategories' => $category->subcategory])
                                                        @endif
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">ارسال مقاله</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
    <script>
        CKEDITOR.replace('body', {
            filebrowserUploadUrl: '{{ route('shop.article.upload-image') }}',
            filebrowserImageUploadUrl: '{{ route('shop.article.upload-image') }}'
        });
    </script>
@endsection
