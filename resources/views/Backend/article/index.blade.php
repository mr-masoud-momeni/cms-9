@extends('Backend.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if(! Auth::user()->hasPermission('123444'))
                dwfvwrfgtg
            @endif
            @if($createpost=session('createpost'))
                <div class="alert alert-success">
                    {{$createpost}}
                </div>
            @endif
            @if($articleupdate=session('articleupdate'))
                 <div class="alert alert-success">
                     {{$articleupdate}}
                 </div>
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">لیست پست ها</div>
                <div class="panel-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>عنوان پست</th>
                            <th>تاریخ ارسال</th>
                            <th>دسته ها</th>
                            <th width="50px"></th>
                            <th width="50px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($articles as $article)

                            <tr class="item{{$article->id}}">
                                <td>{{$article->title}}</td>
                                <td>{{$article->created_at}}</td>
                                <td>
                                    @foreach($article->categories()->get() as $cat)
                                        {{$cat->name}}
                                        @if (!$loop->last)
                                            -
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{route('article.edit',$article->slug)}}"><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>
                                </td>
                                <td>
                                    <a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="{{$article->id}}"><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer">Panel Footer</div>


                <!-- Modal -->
                <div class="modal fade" id="DeleteModal" role="dialog">
                    <div class="modal-dialog margin-top-60">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">حذف گروه</h4>
                            </div>
                            <form method="post" action="{{route('article.delete')}}" class="articleFormDelete">
                                <div class="modal-body">
                                    {!! csrf_field() !!}
                                    {{method_field('delete')}}
                                    <input type="hidden" id="articleDeleteID" name="id" value="">
                                    <p>آیا از حذف این پست اطمینان دارید؟</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">خیر</button>
                                    <button type="submit"  class="btn btn-success">بله</button>
                                    <span class="pull-right" id="deleteMsg" style="color:#5cb85c;"></span>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <!-- Modal -->


            </div>

        </div>
    </div>

@endsection
@section('scripts')
    <script>
        $(document).on("click", ".deleteAjax", function () {
            var articleId = $(this).data('id');
            $("#DeleteModal .modal-body #articleDeleteID").val( articleId );
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.articleFormDelete').submit(function (event) {
                event.preventDefault();
                var $this = $(this);
                var url = $this.attr('action');
                $.ajax({
                    type: 'POST',
                    url: url,
                    datatype: 'JSON',
                    data: $this.serialize(),
                    success: function(data) {
                        if(data.success){
                            $('#DeleteModal').modal('toggle');
                            $('.item'+data.success.id).delay(5000).remove();
                        }
                    }
                });

            });
        });

    </script>
    <script>
        $('#category').selectpicker();
    </script>
@endsection