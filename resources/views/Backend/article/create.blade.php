@extends('Backend.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Backend.layouts.errors')
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ارسال پست</h3></div>
                <div class="panel-body">


                    <div class="row">
                        <div class="col-md-8">

                            <form action="{{route('article.save')}}" method="post" enctype="multipart/form-data">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label for="title">عنوان پست</label>
                                    <input type="text" name="title" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{old('title')}}">
                                </div>

                                <div class="form-group">
                                    <label for="body">متن پست</label>
                                    <textarea name="body" class="form-control" cols="30" id="body" rows="10" placeholder="متن را وارد کنید.">{{old('body')}}</textarea>
                                </div>
                        </div>
                        <div class="col-md-4">
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
                                                            {{$category->name}}
                                                        </label>
                                                        @if(count($category->subcategory))
                                                            @include('Backend.article.subCategoryList',['subcategories' => $category->subcategory])
                                                        @endif
                                                    </div>
                                                </li>
                                            </ul>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">ارسال پست</button>
                            </form>
                        </div>
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
    </script>
@endsection
