@extends('Customer.layouts.Master')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @include('Customer.layouts.errors')
            <div class="panel panel-default">
                <div class="panel-heading"><h3>ویرایش درگاه پرداخت</h3></div>
                <div class="panel-body">


                    <div class="row">


                        <form action="{{ route('gateways.update', $shop) }}" method="POST">
                            @csrf

                            @php
                                $defaultGateways = ['zarinpal', 'payir', 'nextpay'];
                            @endphp

                            @foreach($defaultGateways as $g)
                                <h4>{{ ucfirst($g) }}</h4>

                                <input type="hidden" name="gateways[{{ $loop->index }}][gateway]" value="{{ $g }}">

                                <div>
                                    <label>Merchant ID</label>
                                    <input type="text" name="gateways[{{ $loop->index }}][merchant_id]"
                                           value="{{ $gateways[$g]->merchant_id ?? '' }}">
                                </div>

                                <div>
                                    <label>API Key</label>
                                    <input type="text" name="gateways[{{ $loop->index }}][api_key]"
                                           value="{{ $gateways[$g]->api_key ?? '' }}">
                                </div>

                                <div>
                                    <label>Secret</label>
                                    <input type="text" name="gateways[{{ $loop->index }}][secret]"
                                           value="{{ $gateways[$g]->secret ?? '' }}">
                                </div>

                                <div>
                                    <label>Callback URL</label>
                                    <input type="url" name="gateways[{{ $loop->index }}][callback_url]"
                                           value="{{ $gateways[$g]->callback_url ?? '' }}">
                                </div>

                                <div>
                                    <label>Sandbox Mode</label>
                                    <input type="checkbox" name="gateways[{{ $loop->index }}][sandbox]"
                                           @if($gateways[$g]->sandbox ?? false) checked @endif>
                                </div>

                                <hr>
                            @endforeach

                            <button type="submit">ذخیره درگاه‌ها</button>
                        </form>


                    </div>


                </div>
                <div class="panel-footer">Panel Footer</div>
            </div>

        </div>
    </div>

@endsection
