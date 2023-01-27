@extends('Backend.layouts.Master')
@section('content')
    @include('Backend.layouts.errors')
    <div id="ajaxvalidate"></div>
    <div class="row">
        <div class="col-lg-12">

            <!--panel-->
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ویرایش نقش کاربر</h3></div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-4">
                            <form method="post" action="{{route('role.update',$Role->id)}}">
                                {!! csrf_field() !!}
                                {{method_field('patch')}}
                                <div class="form-group">
                                    <label for="title">عنوان نقش</label>
                                    <input type="text" name="name" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{$Role->name}}">
                                    <input type="hidden" name="id" class="form-control" id="PermissionID" value="{{$Role->id}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">نامک</label>
                                    <input type="text" name="display_name" class="form-control" id="title" placeholder="نامک را وارد کنید..." value="{{$Role->display_name}}">
                                </div>
                                <div class="form-group">
                                    <label for="title">توضیحات</label>
                                    <textarea name="des_role" class="form-control">{{$Role->description}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="Permission">سطح دسترسی ها: </label>
                                    <select name="permission[]" class="form-control" id="Permission" title=" دسته بندی مورد نظر خود را انتخاب کنید..." multiple>
                                        @foreach( $permissions as $id => $name  )
                                            <option value="{{ $id }}" {{in_array($id, $Role->permissions->pluck('id')->toarray())?'selected':''}}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit"  class="btn btn-success">ایجاد نقش</button>
                            </form>
                        </div>


                        <div class="col-md-8"></div>

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
@endsection