@extends('Backend.layouts.Master')
@section('content')
    @include('Backend.layouts.errors')
    <div id="ajaxvalidate"></div>
    <div class="row">
        <div class="col-lg-12">

            <!--panel-->
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ایجاد منو</h3></div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-4">
                            <form method="post" action="{{route('menu.store')}}" id="SubmitForm">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label for="title">عنوان منو</label>
                                    <input type="text" name="title" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{old('title')}}">
                                </div>
                                <button type="submit"  class="btn btn-success">ایجاد منو </button>
                            </form>
                        </div>


                        <div class="col-md-8">
                            <form method="get" action="{{route('search')}}">
                                <div class="form-group col-md-6" >
                                    <input type="text" name="title" class="form-control" id="title" placeholder="عنوان صفحه" value="{{old('title')}}">
                                </div>
                                <div class="form-group col-md-3">
                                    <button type="submit"  class="btn btn-success">جستجو</button>
                                </div>
                            </form>
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th>عنوان صفحه</th>
                                    <th width="50px"></th>
                                    <th width="50px"></th>
                                    <th width="50px"></th>
                                </tr>
                                </thead>
                                <tbody id="ajaxadd">
                                @foreach($menus as $menu)
                                    <tr class="item{{$menu->id}}">
                                        <td>{{$menu->title}}</td>
                                        <td>
                                            <a href="{{route('menu.edit' , $menu->id)}}" ><i class="fa fa-2x fa-plus" aria-hidden="true"></i></a>
                                        </td>
                                        <td>
                                            <a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="{{$menu->id}}"  data-title="{{$menu->title}}"  ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>
                                        </td>
                                        <td>
                                            <a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="{{$menu->id}}" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{ $menus->links() }}
                        </div>

                    </div>

                </div>
                <div class="panel-footer"></div>
            </div>
            <!--panel-->


            <!-- Modal edit -->
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog margin-top-60">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">ویرایش صفحه</h4>
                        </div>
                        <form method="post" action="" class="FormEdit">
                            <div class="modal-body">
                                {!! csrf_field() !!}
                                {{method_field('patch')}}
                                <div class="form-group">
                                    <label for="ModalEditTitle">عنوان منو</label>
                                    <input type="text" name="title" class="form-control" id="ModalEditTitle" placeholder="عنوان را وارد کنید..." value="">
                                    <input type="hidden" name="id" class="form-control" id="ModalEditID" value="">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">بستن</button>
                                <button type="submit"  class="btn btn-success">ویرایش</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <!-- Modal edit -->

            <!-- Modal delete permission -->
            <div class="modal fade" id="DeleteModal" role="dialog">
                <div class="modal-dialog margin-top-60">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">حذف صفحه</h4>
                        </div>
                        <form method="post" action="" class="FormDelete">
                            <div class="modal-body">
                                {!! csrf_field() !!}
                                {{method_field('delete')}}
                                <input type="hidden" id="DeleteID" name="id" value="">
                                <p>آیا از حذف این صفحه اطمینان دارید؟</p>
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
            <!-- Modal delete permission -->




        </div>
    </div>
 @endsection

 @section('scripts')


    //send request for submit permission
    <script>
        $(document).ready(function () {
            $('#SubmitForm').submit(function (event) {
                event.preventDefault();
                var $this = $(this);
                var url = $this.attr('action');
                $.ajax({
                    url: url,
                    type: 'POST',
                    datatype: 'JSON',
                    data: $this.serialize(),
                    success: function(data) {
                        if($.isEmptyObject(data.error)){
                            $('#ajaxadd').prepend('<tr class="item'+data.success.id+'" style="background-color:#ccf3a5; ">' +
                                '<td>'+data.success.title+'</td>' +
                                '<td><a href="/admin/menu/'+data.success.id+'/edit"><i class="fa fa-2x fa-plus" aria-hidden="true"></i></a></td>' +
                                '<td><a class="editajax" data-toggle="modal" data-target="#myModal" data-id="'+data.success.id+'"><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a></td>' +
                                '<td><a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="'+data.success.id+'" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a></td>' +
                                '</tr>');
                            var d = 1000;
                            for(var i=83; i<=100; i=i+0.1){
                                d  += 10;
                                (function(ii,dd){
                                    setTimeout(function(){
                                        $('#ajaxadd').find('tr').first().css('backgroundColor','hsl(90,100%,'+ii+'%)');
                                    }, dd);
                                })(i,d);
                            }

                        }else{

                            printErrorMsg(data.error);

                        }

                    }
                });
            });
            function printErrorMsg (msg) {

                $("#ajaxvalidate").html('<div class="alert-danger alert"><ul></ul></div>');

                $.each( msg, function( key, value ) {

                    $("#ajaxvalidate").find("ul").append('<li>'+value+'</li>');

                });

            }
        });

    </script>
    <script>
        $(document).on("click", ".editajax", function () {
            var Title = $(this).data('title');
            var ID = $(this).data('id');
            $(".modal-body #ModalEditTitle").val( Title );
            $(".modal-body #ModalEditID").val( ID );
            $(".FormEdit").attr("action", "{{url("/admin/menu/")}}/" + ID);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.FormEdit').submit(function (event) {
                event.preventDefault();
                var $this = $(this);
                var url = $this.attr('action');
                $.ajax({
                    type: 'POST',
                    url: url,
                    datatype: 'JSON',
                    data: $this.serialize(),
                    success: function(data) {
                        if(data.errorValidate){
                            $('#myModal').modal('toggle');
                            printErrorMsg(data.errorValidate);
                        }
                        if(data.success){
                            $('#myModal').modal('toggle');
                            $('.item'+data.success.id).css('backgroundColor','#ccf3a5').empty().append('<td>'+data.success.title+'</td>' +
                                '<td><a href="/admin/menu/'+data.success.id+'/edit"><i class="fa fa-2x fa-plus" aria-hidden="true"></i></a></td>' +
                                '<td><a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="'+data.success.id+'" data-title="'+data.success.title+'" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a></td>' +
                                '<td><a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="'+data.success.id+'" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a></td>');
                            var d = 1000;
                            for(var i=83; i<=100; i=i+0.1){
                                d  += 10;
                                (function(ii,dd){
                                    setTimeout(function(){
                                        $('.item'+data.success.id).css('backgroundColor','hsl(90,100%,'+ii+'%)');
                                    }, dd);
                                })(i,d);
                            }
                        }
                    }
                });

            });
            function printErrorMsg (msg) {

                $("#ajaxvalidate").html('<div class="alert-danger alert"><ul></ul></div>');

                $.each( msg, function( key, value ) {

                    $("#ajaxvalidate").find("ul").append('<li>'+value+'</li>');

                });

            }
        });

    </script>
    <script>
        $(document).on("click", ".deleteAjax", function () {
            var DeleteID = $(this).data('id');
            $("#DeleteModal .modal-body #DeleteID").val( DeleteID );
            $(".FormDelete").attr("action", "{{url("/admin/page/")}}/" + DeleteID );
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.FormDelete').submit(function (event) {
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
                            var d = 1000;
                            for(var i=0; i<=50; i=i+0.1){
                                d  += 10;
                                (function(ii,dd){
                                    setTimeout(function(){
                                        $('.item'+data.success.id).css('backgroundColor','hsl(0,100%,'+i+'%)');
                                    }, dd);
                                })(i,d);
                            }
                            setTimeout(function() {
                                $('.item'+data.success.id).remove();
                            }, 2000);
                        }
                    }
                });

            });
        });

    </script>
 @endsection