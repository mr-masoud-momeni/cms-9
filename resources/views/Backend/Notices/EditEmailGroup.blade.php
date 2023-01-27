@extends('Backend.layouts.Master')
@section('content')
    @include('Backend.layouts.errors')
    <div id="ajaxvalidate"></div>
    <div class="row">
        @if($CreateEmailGroup = session('CreateEmailGroup'))
            <div class="alert alert-success">
                {{$CreateEmailGroup}}
            </div>
        @endif
        <div class="col-lg-12">

            <!--panel-->
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ویرایش دسته بندی ایمیل</h3></div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">
                            <form method="post" action="{{route('email-group.update',$EmailGroup->id)}}" id="EmailGroupForm">
                                {!! csrf_field() !!}
                                {{method_field('patch')}}
                                <div class="form-group">
                                    <label for="title">عنوان گروه</label>
                                    <input type="text" name="title" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{$EmailGroup->name}}">
                                </div>
                                <div class="form-group">
                                    <label for="email">ایمیل ها</label>
                                    <textarea name="email" id="emails" class="form-control" rows="20">{{$EmailGroup->emails}}</textarea>
                                </div>
                                <button type="submit"  class="btn btn-primary">به روز رسانی</button>
                            </form>
                        </div>

                    </div>

                </div>
                <div class="panel-footer"></div>
            </div>
            <!--panel-->

        </div>
    </div>
@endsection
@section('scripts')

    <script>
        $(document).ready(function (){
            const emails = document.querySelector('#emails');
            emails.addEventListener('blur', TestEmails);
            function TestEmails() {
                var email = emails.value;
                email = email.split(/\r?\n/);
                var i;
                let regex = /^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]){2,7}$/;
                for (i=0; i < email.length; i++ ){
                    if(!regex.test(email[i])){
                        email[i] = email[i] + "(یک ایمیل وارد کنید)";
                        res = false;
                    }
                    else{
                        email[i] = email[i];
                    }
                }
                document.getElementById("emails").value = "";
                var length = email.length;
                for (i=0; i < length; i++ ){
                    if(i+1 == length){
                        document.getElementById("emails").value += email[i];
                    }
                    else {
                        document.getElementById("emails").value += email[i] + "\n";
                    }
                }
            }
            $( "#EmailGroupForm" ).submit(function( event ) {
                var res1 = TestEmailsLast();
                if(!res1){
                    event.preventDefault();
                    alert('ایمیل ها صحیح نیستند');
                }
            });
            function TestEmailsLast() {
                var email = emails.value;
                var res = true;
                email = email.split(/\r?\n/);
                var i;
                let regex = /^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]){2,7}$/;
                for (i=0; i < email.length; i++ ){
                    if(!regex.test(email[i])){
                        res = false;

                    }
                }
                return res;
            }
        });

    </script>

@endsection