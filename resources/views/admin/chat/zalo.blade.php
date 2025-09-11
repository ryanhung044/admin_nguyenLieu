@extends('admin.layout')

@section('content')
    <a href="{{ route('admin.zalo.login') }}">
        <button>Đăng nhập bằng Zalo</button>
    </a>
    <div class="zalo-chat-widget" data-oaid="738042415649016822" data-welcome-message="Rất vui khi được hỗ trợ bạn!"
        data-autopopup="0" data-width="" data-height=""></div>

    <script src="https://sp.zalo.me/plugins/sdk.js"></script>
@endsection
