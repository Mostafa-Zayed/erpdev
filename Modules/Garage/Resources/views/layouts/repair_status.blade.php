<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title> 

    @include('layouts.partials.css')

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>

<style>
    .eq-height-row{
        height: 100vh;
    }
    .justify-content-between{
        /*justify-content: space-between;*/
    }
    select.input-sm{
        width: 150px;
        display: inline-block;
    }
    .bg-main{
        background: #2c0659;
        border-radius: 3px;
        padding: 5px 10px;
        color: #fff;
    }
    .bg-main:hover{
        color: #fff;
    }
</style>
<body>
    @if (session('status'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}" data-msg="{{ session('status.msg') }}">
    @endif
    @inject('request', 'Illuminate\Http\Request')
    <div class="container-fluid">
        <div class="row eq-height-row justify-content-between">
            <!--<div class="col-md-5 col-sm-5 hidden-xs left-col eq-height-col" >-->
            <!--    <div class="left-col-content login-header"> -->
            <!--        <div style="margin-top: 50%;">-->
            <!--        <a href="/">-->
            <!--        @if(file_exists(public_path('uploads/logo.png')))-->
            <!--            <img src="/uploads/logo.png" class="img-rounded" alt="Logo" width="150">-->
            <!--        @else-->
            <!--           {{ config('app.name', 'ultimatePOS') }}-->
            <!--        @endif -->
            <!--        </a>-->
            <!--        <br/>-->
            <!--        @if(!empty(config('constants.app_title')))-->
            <!--            <small>{{config('constants.app_title')}}</small>-->
            <!--        @endif-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
                <div class="container">
                    <!--<div class="col-md-3 col-xs-4" style="text-align: left;">-->
                        <select class="form-control input-sm" id="change_lang" style="margin: 10px;">
                        @foreach(config('constants.langs') as $key => $val)
                            <option value="{{$key}}" 
                                @if( (empty(request()->lang) && config('app.locale') == $key) 
                                || request()->lang == $key) 
                                    selected 
                                @endif
                            >
                                {{$val['full_name']}}
                            </option>
                        @endforeach
                        </select>
                    <!--</div>-->
                    <!--<div class="col-md-9 col-xs-8" style="text-align: right;padding-top: 10px;">-->
                        @if(!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                            <!-- Register Url -->
                            @if(config('constants.allow_registration'))
                                <a href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif" class="btn bg-main btn-flat" ><b>{{ __('business.not_yet_registered')}}</b> {{ __('business.register_now') }}</a>
                                <!-- pricing url -->
                                @if(Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
                                    &nbsp; <a href="{{ action('\Modules\Superadmin\Http\Controllers\PricingController@index') }}" class="btn bg-main btn-flat">@lang('superadmin::lang.pricing')</a>
                                @endif
                            @endif
                        @endif
                        @if($request->segment(1) != 'login')
                            &nbsp; &nbsp;<span class="text-white">{{ __('business.already_registered')}} </span>
                                <a href="{{ action('Auth\LoginController@login') }}@if(!empty(request()->lang)){{'?lang=' . request()->lang}} @endif" class="btn bg-main btn-flat">{{ __('business.sign_in') }}</a>
                        @endif
                    <!--</div>-->
                    @yield('content')
                </div>
        </div>
    </div>
    <!-- Scripts -->
    @include('layouts.partials.javascripts')
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.select2_register').select2();

            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });

            $('#change_lang').change( function(){
                window.location = "{{ route('repair-status') }}?lang=" + $(this).val();
            });
        });
    </script>
</body>

</html>