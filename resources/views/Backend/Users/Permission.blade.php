@extends('Backend.layouts.Master')
@section('content')
@include('Backend.layouts.errors')
<div id="ajaxvalidate"></div>
    <div class="row">
        <div class="col-lg-12">

            <!--panel-->
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ایجاد سطح دسترسی</h3></div>
                <div class="panel-body">

                    <div class="row">


                        <div class="col-md-4">
                            <form method="post" action="{{route('Permission.store')}}" id="PermissionForm">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label for="title">عنوان سطح دسترسی</label>
                                    <input type="text" name="name" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{old('title')}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">نامک</label>
                                    <input type="text" name="display_name" class="form-control" id="title" placeholder="نامک را وارد کنید..." value="{{old('display_name')}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">توضیحات</label>
                                    <textarea name="description" class="form-control"></textarea>
                                </div>
                                <button type="submit"  class="btn btn-success">ایجاد سطح دسترسی </button>
                            </form>
                        </div>


                        <div class="col-md-8">
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th>عنوان سطح دسترسی</th>
                                    <th>نامک</th>
                                    <th>توضیحات</th>
                                    <th width="50px"></th>
                                    <th width="50px"></th>
                                </tr>
                                </thead>
                                <tbody id="ajaxadd">

                                    @foreach($Permissions as $Permission)
                                        <tr class="item{{$Permission->id}}">
                                            <td>{{$Permission->name}}</td>
                                            <td>{{$Permission->display_name}}</td>
                                            <td>{{$Permission->description}}</td>
                                            <td>
                                                <a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="{{$Permission->id}}"  data-name="{{$Permission->name}}" data-display_name="{{$Permission->display_name}}" data-description="{{$Permission->description}}" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>
                                            </td>
                                            <td>
                                                <a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="{{$Permission->id}}" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
                <div class="panel-footer"></div>
            </div>
            <!--panel-->


            <!-- Modal edit Permission-->
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog margin-top-60">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">ویرایش سطح دسترسی</h4>
                        </div>
                        <form method="post" action="" class="PermissionFormEdit">
                            <div class="modal-body">
                                {!! csrf_field() !!}
                                {{method_field('patch')}}
                                <div class="form-group">
                                    <label for="PermissionTitle">عنوان سطح دسترسی</label>
                                    <input type="text" name="name" class="form-control" id="PermissionTitle" placeholder="عنوان را وارد کنید..." value="">
                                    <input type="hidden" name="id" class="form-control" id="PermissionID" value="">
                                </div>
                                <div class="form-group">
                                    <label for="PermissionLabel">نامک</label>
                                    <input type="text" name="display_name" class="form-control" id="Permissiondisplay_name" placeholder="نامک را وارد کنید..." value="">
                                </div>
                                <div class="form-group">
                                    <label for="title">توضیحات</label>
                                    <textarea name="description" id="Permissiondescription" class="form-control"></textarea>
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
            <!-- Modal edit Permission -->

            <!-- Modal delete permission -->
            <div class="modal fade" id="DeleteModal" role="dialog">
                <div class="modal-dialog margin-top-60">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">حذف سطح دسترسی</h4>
                        </div>
                        <form method="post" action="" class="PermissionFormDelete">
                            <div class="modal-body">
                                {!! csrf_field() !!}
                                {{method_field('delete')}}
                                <input type="hidden" id="PermissionDeleteID" name="id" value="">
                                <p>آیا از حذف این سطح دسترسی اطمینان دارید؟</p>
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
<?php //$url=url('/Permission/create'); ?>
@endsection

@section('scripts')
    <script>
        $(document).on("click", ".deleteAjax", function () {
            var PermissionID = $(this).data('id');
            $("#DeleteModal .modal-body #PermissionDeleteID").val( PermissionID );
            $(".PermissionFormDelete").attr("action", "{{url("/admin/Permission/")}}/" + PermissionID);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.PermissionFormDelete').submit(function (event) {
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
        $(document).ready(function () {
            $('.PermissionFormEdit').submit(function (event) {
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
                            $('.item'+data.success.id).css('backgroundColor','#ccf3a5').empty().append('<td>'+data.success.name+'</td><td>'+data.success.display_name+'</td><td>'+data.success.description+'</td><td><a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="'+data.success.id+'" data-name="'+data.success.name+'" data-display_name="'+data.success.display_name+'" data-description="'+data.success.description+'" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a></td><td><a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="'+data.success.id+'" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a></td>');
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
        $(document).on("click", ".editajax", function () {
            var PermissionTitle = $(this).data('name');
            var PermissionID = $(this).data('id');
            var Permissiondisplay_name = $(this).data('display_name');
            var Permissiondescription = $(this).data('description');
            $(".modal-body #PermissionTitle").val( PermissionTitle );
            $(".modal-body #PermissionID").val( PermissionID );
            $(".modal-body #Permissiondisplay_name").val( Permissiondisplay_name );
            $(".modal-body #Permissiondescription").val( Permissiondescription );
            $(".PermissionFormEdit").attr("action", "{{url("/admin/Permission/")}}/" + PermissionID);
        });
    </script>
    //send request for submit permission
    <script>
        $(document).ready(function () {
            $('#PermissionForm').submit(function (event) {
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
                            $('#ajaxadd').prepend('<tr class="item'+data.success.id+'" style="background-color:#ccf3a5; "><td>'+data.success.name+'</td><td>'+data.success.display_name+'</td><td>'+data.success.description+'</td><td><a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="'+data.success.id+'" data-name="'+data.success.name+'" data-slug="'+data.success.display_name+'" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a></td><td><a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="'+data.success.id+'" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a></td></tr>');
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

@endsection
