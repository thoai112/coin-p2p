@extends($activeTemplate . 'layouts.app')
@section('main-content')
    @include($activeTemplate . 'partials.header', ['langDetails' => $langDetails])
    @yield('content',['langDetails' => $langDetails])
    @include($activeTemplate . 'partials.footer')
@endsection
