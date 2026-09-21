@extends('Customer.layouts.Master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Customer.layouts.errors')

            <div class="panel panel-default">
                <div class="panel-heading"><h3>ویرایش مقاله</h3></div>
                <div class="panel-body">
                    <form action="{{ route('shop.article.update', $article->slug) }}" method="post" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        {{ method_field('patch') }}

                        @php
                            $body = old('body', $article->body);
                            $body = preg_replace('/<br\\s*\\/?\\s*>/i', "\n", $body);
                            $body = preg_replace('/<\\/(p|div|li|h[1-6])>/i', "\n", $body);
                            $body = trim(strip_tags(html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8')));

                            $currentImage = is_array($article->images)
                                ? ($article->images['original'] ?? $article->images['thum'] ?? null)
                                : $article->images;
                        @endphp

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="title">عنوان مقاله</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                           value="{{ old('title', $article->title) }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="body">متن مقاله</label>
                                    <textarea name="body" class="form-control" id="body" rows="12"
                                              placeholder="متن مقاله را وارد کنید..." required>{{ $body }}</textarea>
                                    <small class="text-muted">متن ساده بنویسید؛ Enter و فاصله‌ها حفظ می‌شوند.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="images">تصویر مقاله</label>

                                    @if($currentImage)
                                        <div style="margin-bottom:12px;">
                                            <img src="{{ asset(ltrim($currentImage, '/')) }}"
                                                 alt="{{ $article->title }}"
                                                 style="display:block;width:140px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                        </div>
                                    @endif

                                    <input type="file" name="images" id="images" class="form-control"
                                           accept="image/jpeg,image/png,image/webp">
                                    <div id="imagePreview" style="margin-top:12px;display:none;">
                                        <img id="imagePreviewImage" src="" alt="پیش‌نمایش تصویر جدید"
                                             style="display:block;width:140px;height:140px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
                                    </div>
                                    <small class="text-muted">برای جایگزینی تصویر، فایل جدید را انتخاب کنید.</small>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block">ذخیره تغییرات</button>
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
