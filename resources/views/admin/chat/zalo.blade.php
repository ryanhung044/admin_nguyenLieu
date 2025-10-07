@extends('admin.layout')

@section('content')
    {{-- <a href="{{ route('admin.zalo.login') }}">
        <button>Đăng nhập bằng Zalo</button>
    </a> --}}
    {{-- <iframe src="https://chat.zalo.me/index.html" frameborder="0" style="@auth
            width: 100%;
    height: 100vh;
    @endauth"></iframe> --}}
<!-- Nút mở modal -->
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#zaloChatModal">
    Mở Zalo Chat
</button>
<a href="zalo://">Mở Zalo App</a>


<!-- Modal -->
<div class="modal fade" id="zaloChatModal" tabindex="-1" aria-labelledby="zaloChatModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content" style="height: 80vh;">
      <div class="modal-header">
        <h5 class="modal-title" id="zaloChatModalLabel">Zalo Chat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="zaloChatFrame" src="" frameborder="0" style="width:100%;height:100%;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('zaloChatModal');
    const iframe = document.getElementById('zaloChatFrame');

    modal.addEventListener('show.bs.modal', function () {
        iframe.src = "https://chat.zalo.me/"; // chỉ load khi mở modal
    });

    modal.addEventListener('hidden.bs.modal', function () {
        iframe.src = ""; // clear khi đóng modal để nhẹ bộ nhớ
    });
});
</script>


    <div class="zalo-chat-widget" data-oaid="738042415649016822" data-welcome-message="Rất vui khi được hỗ trợ bạn!"
        data-autopopup="0" data-width="" data-height=""></div>

    <script src="https://sp.zalo.me/plugins/sdk.js"></script>
@endsection
