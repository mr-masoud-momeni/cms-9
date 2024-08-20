@extends('Backend.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Backend.layouts.errors')
            <div class="panel panel-default">
                <div class="panel-heading"><h3>اضافه کردن کاربر</h3></div>
                <div class="panel-body">


                    <div class="row">
                        <div class="col-md-6">

                            <form action="{{route('register.store')}}" method="post" >
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="name">نام</label>
                                    <input type="text" name="name" class="form-control" id="name"  value="{{ old('name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">ایمیل</label>
                                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">پسورد</label>
                                    <input type="password" name="password" class="form-control" id="password" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">تکرار پسورد</label>
                                    <input type="password" name="password_confirmation" class="form-control" id="password-confirm" required>
                                </div>
                                <div class="form-group">
                                    <label >سطح دسترسی</label><br><br>
                                    @foreach($permissions as $permission)
                                        <label class="checkbox-inline">
                                        <input type="checkbox" name="permission[]" value="{{$permission->id}}">{{$permission->name}}
                                        </label>
                                    @endforeach
                                </div><br>
                                <div class="form-group">
                                    <label >نقش کاربر</label><br><br>
                                    @foreach($Roles as $Role)
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="Role[]" value="{{$Role->id}}">{{$Role->name}}
                                        </label>
                                    @endforeach
                                </div>
                                <div class="form-group">
                                    <label for="name-store">نام فروشگاه</label>
                                    <input type="text" name="nameStore" class="form-control" id="nameStore"  value="{{ old('nameStore') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="name">نام انگلیسی</label>
                                    <input type="text" name="nameStoreEn" class="form-control" id="nameStoreEn"  value="{{ old('nameStoreEn') }}" >
                                </div>
                                <div class="form-group">
                                    <label for="name">نام دامنه</label>
                                    <input type="text" name="domain" class="form-control" id="domain"  value="{{ old('domain') }}" >
                                </div>

                            <button type="submit" class="btn btn-success">ایجاد کاربر</button>
                            </form>

                        </div>
                        <div class="col-md-6">
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th>نام</th>
                                    <th>ایمیل</th>
                                    <th width="50px"></th>
                                    <th width="50px"></th>
                                </tr>
                                </thead>
                                <tbody id="ajaxadd">

                                @foreach($Users as $User)
                                    <tr class="item{{$User->id}}">
                                        <td>{{$User->name}}</td>
                                        <td>{{$User->email}}</td>
                                        <td>
                                            <a href="{{route('register.edit',$User->uuid)}}"><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>
                                        </td>
                                        <td>
                                            <a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="{{$User->uuid}}" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>

                        </div>
                    </div>


                </div>
                <div class="panel-footer">Panel Footer</div>
            </div>
            <!-- Modal delete Role -->
            <div class="modal fade" id="DeleteModal" role="dialog">
                <div class="modal-dialog margin-top-60">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">حذف کاربر</h4>
                        </div>
                        <form method="post" action="" class="UserFormDelete">
                            <div class="modal-body">
                                {!! csrf_field() !!}
                                {{method_field('delete')}}
                                <input type="hidden" id="UserDeleteID" name="id" value="">
                                <p>آیا از حذف این کاربر اطمینان دارید؟</p>
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
            var UserID = $(this).data('id');
            $("#DeleteModal .modal-body #UserDeleteID").val( UserID );
            $(".UserFormDelete").attr("action", "{{url("/admin/user/")}}/" + UserID);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.UserFormDelete').submit(function (event) {
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

@endsection
