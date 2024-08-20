@extends('Backend.layouts.Master')
@section('scripts')

@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Backend.layouts.errors')
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ویرایش کاربر</h3></div>
                <div class="panel-body">


                    <div class="row">
                        <div class="col-md-6">

                            <form action="{{route('register.update',$User->id)}}" method="post" >
                                {{ csrf_field() }}
                                {{method_field('patch')}}
                                <div class="form-group">
                                    <label for="name">نام</label>
                                    <input type="text" name="name" class="form-control" id="name"  value="{{$User->name}}" >
                                </div>

                                <div class="form-group">
                                    <label for="email">ایمیل</label>
                                    <input type="email" name="email" class="form-control" id="email" value="{{$User->email}}" >
                                </div>
                                <div class="form-group">
                                    <label for="password">پسورد</label>
                                    <input type="password" name="password" class="form-control" id="password" value="" autocomplete="new-password">
                                </div>
                                <div class="form-group">
                                    <label for="password">تکرار پسورد</label>
                                    <input type="password" name="password_confirmation" class="form-control" id="password-confirm" >
                                </div>
                                <div class="form-group">
                                    <label >سطح دسترسی</label><br><br>
                                    @if($User->permissions)
                                        @foreach($permissions as $permission)
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="permission[]" value="{{$permission->id}}" {{in_array($permission->id, $User->permissions->pluck('id')->toarray())?'checked':''}} >{{$permission->name}}
                                            </label>
                                        @endforeach
                                    @else
                                        @foreach($permissions as $permission)
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="permission[]" value="{{$permission->id}}" >{{$permission->name}}
                                            </label>
                                        @endforeach
                                    @endif
                                </div><br>
                                <div class="form-group">
                                    <label >نقش کاربر</label><br><br>
                                    @if(isset($User->Roles))
                                        @foreach($Roles as $Role)
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="Role[]" value="{{$Role->id}}" {{in_array($Role->id, $User->Roles->pluck('id')->toarray())?'checked':''}}>{{$Role->name}}
                                            </label>
                                        @endforeach
                                    @else
                                        @foreach($Roles as $Role)
                                            <label class="checkbox-inline">
                                                <input type="checkbox" name="Role[]" value="{{$Role->name}}">{{$Role->name}}
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="name-store">نام فروشگاه</label>
                                    <input type="text" name="nameStore" class="form-control" id="nameStore"  value="@if(isset($User->shop->first()->name)){{ $User->shop->first()->name }}@endif" required>
                                </div>
                                <div class="form-group">
                                    <label for="name">نام انگلیسی</label>
                                    <input type="text" name="nameStoreEn" class="form-control" id="nameStoreEn"  value="@if(isset($User->shop->first()->slug)){{ $User->shop->first()->slug }}@endif" >
                                </div>
                                <div class="form-group">
                                    <label for="name">نام دامنه</label>
                                    <input type="text" name="domain" class="form-control" id="domain"  value="@if(isset($User->shop->first()->domain)){{ $User->shop->first()->domain }}@endif" >
                                </div>
                                <button type="submit" class="btn btn-success">ویرایش کاربر</button>
                            </form>

                        </div>
                        <div class="col-md-6">


                        </div>
                    </div>


                </div>
                <div class="panel-footer">Panel Footer</div>
            </div>

        </div>
    </div>

@endsection
