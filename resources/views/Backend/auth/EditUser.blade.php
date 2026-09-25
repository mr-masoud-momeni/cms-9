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
                            <form action="{{route('register.update',$User->id)}}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                {{method_field('patch')}}
                                <div class="form-group">
                                    <label for="name">نام</label>
                                    <input type="text" name="name" class="form-control" id="name"  value="{{$User->name}}" >
                                </div>

                                <div class="form-group">
                                    <label for="phone">شماره همراه</label>
                                    <input type="text" name="phone" class="form-control" id="phone" value="{{ $User->phone }}" required dir="ltr">
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
                                    <input type="text" name="domain" class="form-control" id="domain"  value="@if(isset($User->shop->first()->domain)){{ $User->shop->first()->domain }}@endif" required dir="ltr">
                                </div>
                                <div class="form-group">
                                    <label for="description">توضیحات فروشگاه</label>
                                    <textarea name="description" class="form-control" id="description" rows="3">{{ $User->shop->first()->description ?? '' }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="logo">لوگوی فروشگاه</label>
                                    @if($User->shop->first()->logo ?? null)<div style="margin-bottom:10px;"><img src="{{ asset($User->shop->first()->logo) }}" alt="لوگوی فروشگاه" style="max-width:100px;max-height:100px;"></div>@endif
                                    <input type="file" name="logo" class="form-control" id="logo" accept="image/jpeg,image/png,image/webp">
                                </div>
                                <button type="submit" class="btn btn-success">ذخیره تغییرات</button>
                            </form>

                        </div>
                        <div class="col-md-6">
                            @if($User->shop)
                                <div class="panel panel-info">
                                    <div class="panel-heading">دسترسی فروشنده</div>
                                    <div class="panel-body">
                                        <p><strong>لینک ورود:</strong></p>
                                        @php
                                            $loginDomain = trim($User->shop->domain, " /\\");
                                            if (!preg_match('#^https?://#i', $loginDomain)) {
                                                $loginDomain = 'https://' . $loginDomain;
                                            }
                                        @endphp
                                        <p dir="ltr">
                                            <a href="{{ rtrim($loginDomain, '/') }}/shop/{{ $User->path }}/login" target="_blank">
                                                {{ rtrim($loginDomain, '/') }}/shop/{{ $User->path }}/login
                                            </a>
                                        </p>
                                        <form method="post" action="{{ route('register.password.regenerate', $User->uuid) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm"
                                                    onclick="return confirm('با تولید رمز جدید، رمز فعلی فروشنده دیگر قابل استفاده نخواهد بود. ادامه می‌دهید؟');">
                                                تولید رمز جدید
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>


                </div>
                <div class="panel-footer">Panel Footer</div>
            </div>

        </div>
    </div>

@endsection
