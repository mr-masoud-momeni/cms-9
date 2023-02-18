@extends('Backend.layouts.Master')
@section('content')
@include('Backend.layouts.errors')
<div id="ajaxvalidate"></div>
    <div class="row">
        <div class="col-lg-12">
            <!--panel-->
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ایجاد نقش کاربر</h3></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <form method="post" action="{{route('role.store')}}" id="RoleForm">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label for="title">عنوان نقش</label>
                                    <input type="text" name="name" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{old('name')}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">نامک</label>
                                    <input type="text" name="display_name" class="form-control" id="title" placeholder="نامک را وارد کنید..." value="{{old('display_name')}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">توضیحات</label>
                                    <textarea name="des-role" class="form-control">{{old('des-role')}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="Permission">سطح دسترسی ها: </label>
                                    <select name="permission[]" class="form-control" id="Permission" title=" دسته بندی مورد نظر خود را انتخاب کنید..." multiple>
                                        @foreach( $Permissions as $Permission )
                                            <option value="{{$Permission->id }}">{{$Permission->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit"  class="btn btn-success">ایجاد نقش</button>
                            </form>
                        </div>
                        <div class="col-md-8">
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th>عنوان نقش</th>
                                    <th>نامک</th>
                                    <th>توضیحات</th>
                                    <th width="50px"></th>
                                    <th width="50px"></th>
                                </tr>
                                </thead>
                                <tbody id="ajaxadd">

                                    @foreach($Roles as $Role)
                                        <tr class="item{{$Role->id}}">
                                            <td>{{$Role->name}}</td>
                                            <td>{{$Role->display_name}}</td>
                                            <td>{{$Role->description}}</td>
                                            <td>
                                                <a href="{{route('role.edit',$Role->id)}}"><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>
                                            </td>
                                            <td>
                                                <a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="{{$Role->id}}" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>
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

            <!-- Modal delete Role -->
            <div class="modal fade" id="DeleteModal" role="dialog">
                <div class="modal-dialog margin-top-60">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">حذف سطح دسترسی</h4>
                        </div>
                        <form method="post" action="" class="RoleFormDelete">
            <div class="modal-body">
                {!! csrf_field() !!}
                {{method_field('delete')}}
                <input type="hidden" id="RoleDeleteID" name="id" value="">
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
<!-- Modal delete Role -->




        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('#Permission').selectpicker();
    </script>
    <script>
        $(document).on("click", ".deleteAjax", function () {
            var RoleID = $(this).data('id');
            $("#DeleteModal .modal-body #RoleDeleteID").val( RoleID );
            $(".RoleFormDelete").attr("action", "{{url("/admin/role/")}}/" + RoleID);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.RoleFormDelete').submit(function (event) {
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
            $('#RoleForm').submit(function (event) {
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
                            $('#ajaxadd').prepend('<tr class="item'+data.success.id+'" style="background-color:#ccf3a5; "><td>'+data.success.name+'</td><td>'+data.success.display_name+'</td><td>'+data.success.description+'</td><td><a href="Role/'+data.success.id+'/edit"><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a></td><td><a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="'+data.success.id+'" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a></td></tr>');
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