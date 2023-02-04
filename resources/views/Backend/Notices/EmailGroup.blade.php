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
    @if($EditEmailGroup = session('EditEmailGroup'))
        <div class="alert alert-success">
            {{$EditEmailGroup}}
        </div>
    @endif
    <div class="col-lg-12">

        <!--panel-->
        <div class="panel panel-default">
            <div class="panel-heading"><h3>ایجاد دسته بندی ایمیل</h3></div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-md-12">
                        <form method="post" action="{{route('email-group.store')}}" id="EmailGroupForm">
                            {!! csrf_field() !!}
                            <div class="form-group">
                                <label for="title">عنوان گروه</label>
                                <input type="text" name="title" class="form-control" id="title" placeholder="عنوان را وارد کنید..." value="{{old('title')}}">
                            </div>
                            <div class="form-group">
                                <label for="email">ایمیل ها</label>
                                <textarea name="email" id="emails" class="form-control" rows="20" placeholder="هر ایمیل را در یک سطر وارد نمایید..."></textarea>
                            </div>
                            <button type="submit"  class="btn btn-success">ایجاد گروه</button>
                        </form>
                    </div>

                </div>
                <div class="row" style="margin-top: 50px;">
                    <div class="col-md-12">
                        <table class="table ">
                            <thead>
                            <tr>
                                <th>عنوان گروه</th>
                                <th width="50px"></th>
                                <th width="50px"></th>
                            </tr>
                            </thead>
                            <tbody id="ajaxadd">
                                @foreach($EmailGroups as $EmailGroup)
                                    <tr class="item{{$EmailGroup->id}}">
                                        <td>{{$EmailGroup->name}}</td>
                                        <td>
                                            <a href="{{route('email-group.edit',$EmailGroup->id)}}" ><i class="fa fa-2x fa-pencil-square-o" aria-hidden="true"></i></a>
                                        </td>
                                        <td>
                                            <a class="deleteAjax" data-toggle="modal" data-target="#DeleteModal" data-id="{{$EmailGroup->id}}" ><i class="fa fa-2x fa-trash-o" aria-hidden="true"></i></a>
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
        <div class="modal fade" id="DeleteModal" role="dialog">
            <div class="modal-dialog margin-top-60">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">حذف گروه</h4>
                    </div>
                    <form method="post" action="" class="EmailGroupDelete">
                        <div class="modal-body">
                            {!! csrf_field() !!}
                            {{method_field('delete')}}
                            <input type="hidden" id="EmailGroupDeleteID" name="id" value="">
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
@endsection
@section('scripts')
    <script>
        $(document).on("click", ".deleteAjax", function () {
            var categoryId = $(this).data('id');
            $("#DeleteModal .modal-body #EmailGroupDeleteID").val( categoryId );
            $(".EmailGroupDelete").attr("action", "{{url("/admin/email-group/")}}/" + categoryId);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.EmailGroupDelete').submit(function (event) {
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
        $(document).ready(function (){
            const emails = document.querySelector('#emails');
            /*Add Event when the focus from element are removed */
            emails.addEventListener('blur', TestEmails);
            function TestEmails() {
                var email = emails.value;
                /*split string to a array with split function*/
                email = email.split(/\r?\n/);
                var i;
                let regex = /^([_\-\.0-9a-zA-Z]+)@([_\-\.0-9a-zA-Z]+)\.([a-zA-Z]){2,7}$/;
                for (i=0; i < email.length; i++ ){
                    /* Test elements from array for indicate one of element is email or no */
                    if(!regex.test(email[i])){
                        email[i] = email[i] + "(یک ایمیل وارد کنید)";
                        res = false;
                    }
                    else{
                        email[i] = email[i];
                    }
                }
                //after checking emails, add emails to textarea
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

            //check emails after submit the form
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