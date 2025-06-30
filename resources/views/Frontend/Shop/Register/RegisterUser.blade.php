@extends('Frontend.Shop.layouts.Master')
@section('Main')
    <!-- صفحه محصول -->
    <div class="content container my-5">
        <div class="row align-items-center">
            <!-- ثبت نام یوزر -->
            <div class="col-md-6">
                <!-- فرم ثبت‌نام -->
                <form action="{{ route('buyer.register') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" id="name" class="form-control" placeholder="نام" name="name" value="{{ old('name') }}">
                    </div>
                    <div class="form-group mt-3">
                        <input type="email" id="email" class="form-control" placeholder="ایمیل" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="form-group mt-3">
                        <input type="text" id="phone" class="form-control" placeholder="تلفن همراه" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="text-center mt-3"><button type="submit" class="btn btn-success">{{ __('ui.register')}}</button></div>
                </form>
            </div>
            <!-- خالی -->
            <div class="col-md-6 text-center">
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        jQuery(document).ready(function($){
            $('.AddProduct').submit(function (event) {
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
                            var order = $("#cart-val").attr('value');
                            order = Number(order);
                            order = order+data.success;
                            $("#cart-val").attr('value', order);
                            showToast(data.message, "success");

                        }else{

                            showToast(data.message, "danger");

                        }

                    }
                });
            });
            function showToast(message, type) {
                const toastHTML = `
                                    <div class="toast align-items-center bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                                      <div class="d-flex">
                                        <div class="toast-body">${message}</div>
                                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                      </div>
                                    </div>
                                  `;

                const container = document.querySelector('#toastContainer');
                container.innerHTML = toastHTML;
                const toast = new bootstrap.Toast(container.querySelector('.toast'), { delay: 3000 });
                toast.show();
            }
        });

    </script>
@endsection
