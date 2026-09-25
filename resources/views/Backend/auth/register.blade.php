@extends('Backend.layouts.Master')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Backend.layouts.errors')

            @if(session('credentials'))
                @php($credentials = session('credentials'))
                <div class="alert alert-success">
                    <h4 style="margin-top:0;">
                        {{ session('password_regenerated') ? 'رمز فروشنده تغییر کرد' : 'فروشگاه با موفقیت ایجاد شد' }}
                    </h4>
                    <p>این اطلاعات ورود را همین حالا کپی و برای فروشنده ارسال کنید. رمز عبور فقط در همین مرحله نمایش داده می‌شود.</p>

                    <div class="well" id="sellerCredentials" style="margin-bottom:10px;">
                        <div><strong>فروشگاه:</strong> {{ $credentials['shop_name'] }}</div>
                        <div><strong>فروشنده:</strong> {{ $credentials['user_name'] }}</div>
                        <div><strong>شماره همراه:</strong> {{ $credentials['phone'] }}</div>
                        <div><strong>ایمیل:</strong> {{ $credentials['email'] }}</div>
                        <div><strong>لینک ورود:</strong> <a href="{{ $credentials['login_url'] }}" target="_blank">{{ $credentials['login_url'] }}</a></div>
                        <div><strong>رمز عبور:</strong> <code id="generatedPassword">{{ $credentials['password'] }}</code></div>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm" id="copyCredentials">کپی اطلاعات ورود</button>

                    @if(!empty($credentials['uuid']))
                        <form method="post" action="{{ route('register.password.regenerate', $credentials['uuid']) }}" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm"
                                    onclick="return confirm('با تولید رمز جدید، رمز فعلی فروشنده دیگر قابل استفاده نخواهد بود. ادامه می‌دهید؟');">
                                تولید رمز جدید
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <div class="panel panel-default">
                <div class="panel-heading"><h3>ایجاد فروشگاه</h3></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('register.store') }}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <h4>اطلاعات فروشنده</h4>

                                <div class="form-group">
                                    <label for="name">نام</label>
                                    <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="phone">شماره همراه</label>
                                    <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}" required dir="ltr">
                                </div>

                                <div class="form-group">
                                    <label for="email">ایمیل</label>
                                    <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}" required dir="ltr">
                                </div>

                                <h4 style="margin-top:25px;">اطلاعات فروشگاه</h4>

                                <div class="form-group">
                                    <label for="nameStore">نام فروشگاه</label>
                                    <input type="text" name="nameStore" class="form-control" id="nameStore" value="{{ old('nameStore') }}" required>
                                </div>

                                <div class="form-group">
                                    <label for="nameStoreEn">نام انگلیسی</label>
                                    <input type="text" name="nameStoreEn" class="form-control" id="nameStoreEn" value="{{ old('nameStoreEn') }}">
                                </div>

                                <div class="form-group">
                                    <label for="domain">دامنه فروشگاه</label>
                                    <input type="text" name="domain" class="form-control" id="domain" value="{{ old('domain') }}" required dir="ltr">
                                </div>

                                <div class="form-group">
                                    <label for="description">توضیحات فروشگاه</label>
                                    <textarea name="description" class="form-control" id="description" rows="3">{{ old('description') }}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="logo">لوگوی فروشگاه</label>
                                    <input type="file" name="logo" class="form-control" id="logo" accept="image/jpeg,image/png,image/webp">
                                </div>

                                <h4 style="margin-top:25px;">سطح دسترسی</h4>
                                <div class="form-group">
                                    @foreach($permissions as $permission)
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="permission[]" value="{{ $permission->id }}">{{ $permission->name }}
                                        </label>
                                    @endforeach
                                </div>

                                <h4 style="margin-top:25px;">نقش کاربر</h4>
                                <div class="form-group">
                                    @foreach($Roles as $Role)
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="Role[]" value="{{ $Role->id }}">{{ $Role->name }}
                                        </label>
                                    @endforeach
                                </div>

                                <div class="alert alert-info">
                                    رمز عبور به‌صورت خودکار و تصادفی ساخته می‌شود و بعد از ایجاد فروشگاه فقط یک‌بار نمایش داده خواهد شد.
                                </div>

                                <button type="submit" class="btn btn-success">ایجاد فروشگاه</button>
                            </form>
                        </div>

                        <div class="col-md-6">
                            <h4>فروشگاه‌ها</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>فروشگاه</th>
                                        <th>فروشنده</th>
                                        <th>موبایل</th>
                                        <th>ورود</th>
                                        <th width="50px"></th>
                                    </tr>
                                    </thead>
                                    <tbody id="ajaxadd">
                                    @foreach($Users as $User)
                                        <tr class="item{{ $User->id }}">
                                            <td>{{ $User->shop->name ?? '—' }}</td>
                                            <td>{{ $User->name }}</td>
                                            <td dir="ltr">{{ $User->phone ?? '—' }}</td>
                                            <td>
                                                @if($User->shop && $User->shop->domain)
                                                    <a href="{{ preg_match('#^https?://#i', $User->shop->domain) ? rtrim($User->shop->domain, '/') : 'https://' . rtrim($User->shop->domain, '/') }}/shop/{{ $User->path }}/login" target="_blank">ورود</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('register.edit', $User->uuid) }}" title="ویرایش">
                                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '#copyCredentials', function () {
            var box = $('#sellerCredentials');
            var text = box.text().replace(/\\n+/g, '\\n').trim();

            navigator.clipboard.writeText(text).then(function () {
                $('#copyCredentials').text('کپی شد');
                setTimeout(function () {
                    $('#copyCredentials').text('کپی اطلاعات ورود');
                }, 2000);
            });
        });
    </script>
@endsection
