@extends('Backend.layouts.Master')
@section('content')
@include('Backend.layouts.errors')
<div id="ajaxvalidate"></div>
    <div class="row">
        <div class="col-lg-12">

            <!--panel-->
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ایجاد گروه</h3></div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-4">
                            <form method="post" action="{{route('category.save')}}" id="CategoryForm">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label for="title">عنوان گروه</label>
                                    <input type="text" name="name" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{old('title')}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">نامک</label>
                                    <input type="text" name="slug" class="form-control" id="title" placeholder="نامک را وارد کنید..." value="{{old('title')}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">والد</label><br>
                                    <select name="parent_id" id="categoryParent" class="addAndRemoveParent">
                                        <option value="0">بدون والد</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="type" value="{{$Model}}">
                                <button type="submit"  class="btn btn-success">ایجاد گروه</button>
                            </form>
                        </div>


                        <div class="col-md-8">
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th>عنوان گروه</th>
                                    <th>نامک</th>
                                    <th>والد</th>
                                    <th width="50px"></th>
                                    <th width="50px"></th>
                                </tr>
                                </thead>
                                <tbody id="ajaxadd">

                                    @foreach($categories as $category)
                                        <tr class="item{{$category->id}}">
                                            <td>{{$category->name}}</td>
                                            <td>{{$category->slug}}</td>
                                            <td>
                                                @if($category->parent_id)
                                                    @php
                                                        $cat = $categories->toArray();
                                                        $key = array_search($category->parent_id , array_column($cat , 'id'));
                                                    @endphp
                                                    {{$cat[$key]["name"]}}
                                                @elseif(!$category->parent_id)
                                                    @php
                                                        $cat = 0;
                                                        $key = 0;
                                                    @endphp
                                                @else
                                                @endif
                                            </td>
                                            <td>
                                                <a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="{{$category->id}}"  data-name="{{$category->name}}" data-slug="{{$category->slug}}" data-parent="{{$cat[$key]["name"]}}" data-parent-id="{{$category->parent_id}}" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>
                                            </td>
                                            <td>
                                                <a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="{{$category->id}}" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>
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


            <!-- Modal -->
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog margin-top-60">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">ویرایش گروه</h4>
                        </div>
                        <form method="post" action="{{route('category.edit')}}" class="CategoryFormEdit">
                            <div class="modal-body">
                                {!! csrf_field() !!}
                                {{method_field('patch')}}
                                <div class="form-group">
                                    <label for="categoryTitle">عنوان گروه</label>
                                    <input type="text" name="name" class="form-control" id="categoryTitle" placeholder="عنوان را وارد کنید..." value="">
                                    <input type="hidden" name="id" class="form-control" id="categoryID" value="">
                                </div>
                                <div class="form-group">
                                    <label for="categorySlug">نامک</label>
                                    <input type="text" name="slug" class="form-control" id="categorySlug" placeholder="نامک را وارد کنید..." value="{{old('title')}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">والد</label><br>
                                    <select name="parent_id" class="addAndRemoveParent editParent" >
                                        <option id="categoryDefault" value="0">بدون والد</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
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
            <!-- Modal -->


            <!-- Modal -->
            <div class="modal fade" id="DeleteModal" role="dialog">
                <div class="modal-dialog margin-top-60">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">حذف گروه</h4>
                        </div>
                        <form method="post" action="{{route('category.delete')}}" class="CategoryFormDelete">
                            <div class="modal-body">
                                {!! csrf_field() !!}
                                {{method_field('delete')}}
                                <input type="hidden" id="categoryDeleteID" name="id" value="">
                                <p>آیا از حذف این گروه اطمینان دارید؟</p>
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
<?php $url=url('/category/create'); ?>
@endsection

@section('scripts')
    <script>
        $(document).on("click", ".deleteAjax", function () {
            var categoryId = $(this).data('id');
            $("#DeleteModal .modal-body #categoryDeleteID").val( categoryId );
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.CategoryFormDelete').submit(function (event) {
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
                            $('.item'+data.success.id).css('backgroundColor','#ff0000');
                            var d = 1000;
                            for(var i=83; i<=100; i=i+0.1){
                                d  += 10;
                                (function(ii,dd){
                                    setTimeout(function(){
                                        $('.item'+data.success.id).css('backgroundColor','hsl(0,100%,'+ii+'%)');
                                    }, dd);
                                })(i,d);
                            }
                            $('.addAndRemoveParent option[value='+data.success.id+']').remove();
                            setTimeout($('.item'+data.success.id).remove(), 10000);
                        }
                    }
                });

            });
        });

    </script>
    <script>
        $(document).ready(function () {
            $('.CategoryFormEdit').submit(function (event) {
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
                            $('.item'+data.success.id).css('backgroundColor','#ccf3a5').empty().append('<td>'+data.success.name+'</td><td>'+data.success.slug+'</td><td>'+data.parent+'</td><td><a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="'+data.success.id+'" data-name="'+data.success.name+'" data-slug="'+data.success.slug+'" data-parent="'+data.parent+'" data-parent-id="'+data.success.parent_id+'" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a></td><td><a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="'+data.success.id+'" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a></td>');
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
            var categoryName = $(this).data('name');
            var categoryID = $(this).data('id');
            var categorySlug = $(this).data('slug');
            var categoryParent = $(this).data('parent');
            var categoryParentID = $(this).data('parent-id');
            $(".modal-body #categoryTitle").val( categoryName );
            $(".modal-body #categoryID").val( categoryID );
            $(".modal-body #categorySlug").val( categorySlug );
            if(categoryParentID){
                $(".modal-body #categoryDefault").prop("selected", false);
                $('.editParent option[value='+categoryParentID+']').prop("selected", true);
//                $(".modal-body #categoryParent").val( categoryParentID );
//                $(".modal-body #categoryParent").html(categoryParent);
//                $(".modal-body #categoryDefault").prop("selected", false);
//                $(".modal-body #categoryParent").prop("selected", true);
            }
            else {
                $(".modal-body #categoryDefault").prop("selected", true);
            }
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#CategoryForm').submit(function (event) {
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
                            $('#ajaxadd').prepend('<tr class="item'+data.success.id+'" style="background-color:#ccf3a5; "><td>'+data.success.name+'</td><td>'+data.success.slug+'</td>' +
                                '<td>'+data.parent+'</td>' +
                                '<td><a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="'+data.success.id+'" data-name="'+data.success.name+'" data-slug="'+data.success.slug+'" data-parent="'+data.parent+'" data-parent-id="'+data.success.parent_id+'" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a></td>' +
                                '<td><a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="'+data.success.id+'" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a></td></tr>');
                            $('.addAndRemoveParent').prepend('<option value="'+data.success.id+'">'+data.success.name+'</option>');
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
