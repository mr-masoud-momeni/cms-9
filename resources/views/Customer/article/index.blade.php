@extends('Customer.layouts.Master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if($message = session('createarticle'))
                <div class="alert alert-success">{{ $message }}</div>
            @endif

            @if($message = session('articleupdate'))
                <div class="alert alert-success">{{ $message }}</div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <span>لیست مقالات</span>
                    <a href="{{ route('shop.article.create') }}" class="btn btn-primary btn-sm pull-left">ایجاد مقاله</a>
                </div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>عنوان مقاله</th>
                            <th>تاریخ ارسال</th>
                            <th width="50px"></th>
                            <th width="50px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($articles as $article)
                            <tr class="item{{ $article->id }}">
                                <td>{{ $article->title }}</td>
                                <td>{{ $article->created_at }}</td>
                                <td>
                                    <a href="{{ route('shop.article.edit', $article->slug) }}">
                                        <i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td>
                                    <a class="deleteAjax"
                                       data-toggle="modal"
                                       data-target="#DeleteModal"
                                       data-id="{{ $article->id }}"
                                       data-url="{{ route('shop.article.destroy', $article->slug) }}">
                                        <i class="fa fa-2x fa-trash-o" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="text-center">
                        {{ $articles->links() }}
                    </div>
                </div>
            </div>

            <div class="modal fade" id="DeleteModal" role="dialog">
                <div class="modal-dialog margin-top-60">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">حذف مقاله</h4>
                        </div>
                        <form method="post" action="" class="articleFormDelete">
                            <div class="modal-body">
                                {!! csrf_field() !!}
                                {{ method_field('delete') }}
                                <p>آیا از حذف این مقاله اطمینان دارید؟</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">خیر</button>
                                <button type="submit" class="btn btn-success">بله</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.deleteAjax', function () {
            $('.articleFormDelete').attr('action', $(this).data('url'));
        });

        $(document).on('submit', '.articleFormDelete', function (event) {
            event.preventDefault();

            const form = $(this);

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function (data) {
                    if (data.success) {
                        $('#DeleteModal').modal('hide');
                        $('.item' + data.success.id).remove();
                    }
                }
            });
        });
    </script>
@endsection
