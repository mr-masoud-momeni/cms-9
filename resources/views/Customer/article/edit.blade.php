@extends('Customer.layouts.Master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Customer.layouts.errors')

            <div class="panel panel-default">
                <div class="panel-heading">ویرایش مقاله</div>
                <div class="panel-body">
                    <form action="{{ route('shop.article.update', $article->slug) }}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        {{ method_field('patch') }}

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">عنوان مقاله</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                           value="{{ $article->title }}">
                                </div>

                                <div class="form-group">
                                    <label for="body">متن مقاله</label>
                                    <textarea name="body" class="form-control" id="body" rows="12">{{ $article->body }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="images">تصویر شاخص</label>
                                    <input type="file" name="images" id="images">

                                    @if(!empty($article->images['thum']))
                                        <img src="{{ asset($article->images['thum']) }}" class="imageArticle">
                                    @endif

                                    @if(!empty($article->images['images']))
                                        <select name="imageThum">
                                            @foreach($article->images['images'] as $key => $image)
                                                <option value="{{ $image }}" {{ $article->images['thum'] == $image ? 'selected' : '' }}>
                                                    {{ $key }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>دسته‌بندی‌ها</label>
                                    <div class="ShowCategorySelect">
                                        @foreach($parentCategories as $category)
                                            <ul>
                                                <li>
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox" name="category[]" value="{{ $category->id }}"
                                                                {{ $article->categories->pluck('id')->contains($category->id) ? 'checked' : '' }}>
                                                            {{ $category->name }}
                                                        </label>

                                                        @if(count($category->subcategory))
                                                            @include('Customer.article.subCategoryListEdit', [
                                                                'subcategories' => $category->subcategory,
                                                                'article' => $article
                                                            ])
                                                        @endif
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">ویرایش مقاله</button>
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
