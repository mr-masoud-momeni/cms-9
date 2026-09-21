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
                                           value="{{ old('title') }}" placeholder="عنوان را وارد کنید..." required>
                                </div>

                                <div class="form-group">
                                    <label for="body">متن مقاله</label>
                                    <textarea name="body" class="form-control" id="body" rows="12"
                                              placeholder="متن مقاله را وارد کنید..." required>{{ old('body') }}</textarea>
                                    <small class="text-muted">متن ساده بنویسید؛ Enter و فاصله‌ها حفظ می‌شوند.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="images">تصویر مقاله</label>
                                    <input type="file" name="images" id="images" class="form-control"
                                           accept="image/jpeg,image/png,image/webp" required>
                                    <div id="imagePreview" style="margin-top:12px;display:none;">
                                        <img id="imagePreviewImage" src="" alt="پیش‌نمایش تصویر"
                                             style="display:block;width:140px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    </div>
                                    <small class="text-muted">یک تصویر انتخاب کنید. تصویر به WebP تبدیل و تا 800 پیکسل بهینه می‌شود.</small>
                                </div>

                                <button type="submit" class="btn btn-success btn-block">ارسال مقاله</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.getElementById('images').addEventListener('change', function (event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const image = document.getElementById('imagePreviewImage');

    if (!file) {
        preview.style.display = 'none';
        image.removeAttribute('src');
        return;
    }

    image.src = URL.createObjectURL(file);
    preview.style.display = 'block';
});
</script>
@endsection
