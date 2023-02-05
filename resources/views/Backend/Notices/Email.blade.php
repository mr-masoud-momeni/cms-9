@extends('Backend.layouts.Master')
@section('content')
    @include('Backend.layouts.errors')
    <div id="ajaxvalidate"></div>
    <div class="row">
        <div class="col-lg-12">

            <!--panel-->
            <div class="panel panel-default">
                <div class="panel-heading"><h3>پیغام همگانی</h3></div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" action="{{route('email.store')}}" id="NotificationForm">
                                {!! csrf_field() !!}
                                <div class="form-group">
                                    <label for="title">عنوان پیغام</label>
                                    <input type="text" name="title" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{old('title')}}">
                                </div>
                                <div class="form-group">
                                    <label for="Received">گیرنده</label>
                                    <input type="text" name="Received" class="form-control" id="title" placeholder="برای تعداد بیشتر از کاما استفاده کنید" value="{{old('slug')}}">
                                </div>
                                <div class="form-group">
                                    <label for="category">ایمیل های دسته بندی شده</label>
                                    <select name="EmailGroups[]" class="form-control" id="category" title=" دسته بندی مورد نظر خود را انتخاب کنید..." multiple>
                                        @foreach( $EmailGroups as $id => $name )
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="Received-cc">cc</label>
                                    <input type="email" name="Received-cc" class="form-control" id="title" placeholder="برای تعداد بیشتر از کاما استفاده کنید" value="{{old('slug')}}">
                                </div>
                                <div class="form-group">
                                    <label for="body">متن</label>
                                    <textarea name="body" class="form-control"></textarea>
                                </div>
                                <button type="submit"  class="btn btn-success">ارسال ایمیل </button>
                            </form>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table ">
                                <thead>
                                <tr>
                                    <th>عنوان پیغام</th>
                                    <th>گیرنده</th>
                                    <th>متن</th>
                                    <th width="50px"></th>
                                    <th width="50px"></th>
                                </tr>
                                </thead>
                                <tbody id="ajaxadd">

                                {{--@foreach($Notifications as $Notification)--}}
                                {{--<tr class="item{{$Notification->id}}">--}}
                                {{--<td>{{$Notification->title}}</td>--}}
                                {{--<td>{{$Notification->slug}}</td>--}}
                                {{--<td>{{$Notification->body}}</td>--}}
                                {{--<td>--}}
                                {{--<a class="editajax"  data-toggle="modal" data-target="#myModal" data-id="{{$Notification->id}}"  data-title="{{$Notification->title}}" data-slug="{{$Notification->slug}}" data-body="{{$Notification->body}}" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                {{--<a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="{{$Notification->id}}" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>--}}
                                {{--</td>--}}
                                {{--</tr>--}}
                                {{--@endforeach--}}

                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
                <div class="panel-footer"></div>
            </div>
            <!--panel-->

        </div>
    </div>
    <?php //$url=url('/Permission/create'); ?>
    @endsection
