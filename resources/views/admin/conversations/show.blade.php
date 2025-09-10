@extends('admin.layout')

@section('content')
    <div class="row">
        <!-- Cột bên trái: Thông tin hội thoại -->
        <div class="col-md-4 border-end">
            <h4 class="mb-3">Thông tin hội thoại</h4>
            <p><strong>Platform:</strong>
                <span class="badge bg-{{ $conversation->platform == 'zalo' ? 'info' : 'primary' }}">
                    {{ ucfirst($conversation->platform) }}
                </span>
            </p>

            <h4><img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}" alt="avatar"
                    class="rounded-circle" width="50" height="50"> {{ $conversation?->user?->name }}</h4>
            {{-- <h5>Email: {{ $conversation?->user?->email }}</h5> --}}
            <p><strong>Thời gian:</strong>
                {{ $conversation->last_time ? \Carbon\Carbon::parse($conversation->last_time)->format('d/m/Y H:i') : '' }}
            </p>

            <a href="{{ route('admin.conversations.index') }}" class="btn btn-secondary btn-sm">
                ← Quay lại danh sách
            </a>
        </div>

        <!-- Cột bên phải: Khung chat -->
        <div class="col-md-8">
            <div class="card" style="height: 80vh">
                <div class="card-header">
                    <h5 class="mb-0">Hội thoại với {{ $conversation?->user?->full_name }}</h5>
                </div>
                <div id="chat-box" class="card-body" style="height: 500px; overflow-y: auto; background: #f9f9f9;">
                    @foreach ($messages as $msg)
                        <div
                            class="d-flex mb-3 
                            @if ($msg->sender_type == 'admin') justify-content-end 
                            @elseif ($msg->sender_type == 'system') justify-content-center 
                            @else justify-content-start @endif">

                            <div class="p-2 rounded 
                                @if ($msg->sender_type == 'admin') bg-primary text-white 
                                @elseif ($msg->sender_type == 'system') bg-light text-muted fst-italic 
                                @else bg-light border @endif"
                                style="max-width: 70%;">

                                {{-- Hiển thị message --}}
                                @if ($msg->message_type === 'text')
                                    <div>{{ $msg->message_text }}</div>
                                @elseif ($msg->message_type === 'image')
                                    <div><img src="{{ $msg->message_text }}" alt="image" class="img-fluid rounded"></div>
                                @elseif ($msg->message_type === 'sticker')
                                    <div>[Sticker ID: {{ $msg->message_text }}]</div>
                                @elseif ($msg->message_type === 'event')
                                    <div class="text-muted fst-italic">{{ $msg->message_text }}</div>
                                @else
                                    <div>[Không xác định]</div>
                                @endif

                                <small class="text-muted d-block mt-1" style="font-size: 12px;">
                                    {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i d/m/Y ') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer">
                    <form action="{{ route('admin.conversations.send', $conversation->id) }}" method="POST">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="content" class="form-control" placeholder="Nhập tin nhắn..."
                                required>
                            <button type="submit" class="btn btn-primary">Gửi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let chatBox = document.getElementById("chat-box");
            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

            const conversation = @json($conversation); // Laravel helper

            window.Echo.channel(`conversation.${conversation.id}`)
                .listen('.MessageCreated', (e) => {
                    console.log('MessageCreated', e);

                    let msg = e.message;

                    // Tạo div chứa message
                    let div = document.createElement("div");
                    div.className = "d-flex mb-3 " +
                        (msg.sender_type === 'admin' ? "justify-content-end" :
                            msg.sender_type === 'system' ? "justify-content-center" : "justify-content-start");

                    // Xử lý nội dung theo loại message
                    let contentHtml = '';
                    switch (msg.message_type) {
                        case 'text':
                            contentHtml = `<div>${msg.message_text}</div>`;
                            break;
                        case 'image':
                            contentHtml =
                                `<div><img src="${msg.message_text}" alt="image" class="img-fluid rounded"></div>`;
                            break;
                        case 'sticker':
                            contentHtml = `<div>[Sticker ID: ${msg.message_text}]</div>`;
                            break;
                        case 'event':
                            contentHtml = `<div class="text-muted fst-italic">${msg.message_text}</div>`;
                            break;
                        default:
                            contentHtml = `<div>[Không xác định]</div>`;
                    }

                    // Format thời gian
                    let formattedTime = new Date(msg.sent_at).toLocaleString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // Tạo innerHTML
                    div.innerHTML = `
                <div class="p-2 rounded ${msg.sender_type === 'admin' ? 'bg-primary text-white' :
                                        msg.sender_type === 'system' ? 'bg-light text-muted fst-italic' :
                                        'bg-light border'}" style="max-width:70%">
                    ${contentHtml}
                    <small class="text-muted d-block mt-1" style="font-size:12px;">
                        ${formattedTime}
                    </small>
                </div>
            `;

                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        });
    </script>

    {{-- <script>
        // Pusher.logToConsole = true;

        document.addEventListener("DOMContentLoaded", function() {
            let chatBox = document.getElementById("chat-box");
            if (chatBox) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
            const conversation = @json($conversation); // Laravel helper
            console.log(conversation.id);
            window.Echo.channel(`conversation.${conversation.id}`)
                .listen(".MessageCreated", (e) => {
                    console.log('MessageCreated');

                    let msg = e.message;

                    let div = document.createElement("div");
                    div.className = "d-flex mb-3 " +
                        (msg.sender_type === 'admin' ? "justify-content-end" :
                            msg.sender_type === 'system' ? "justify-content-center" : "justify-content-start");

                    div.innerHTML = `
                                        <div class="p-2 rounded ${msg.sender_type === 'admin' ? 'bg-primary text-white' :
                                                                msg.sender_type === 'system' ? 'bg-light text-muted fst-italic' :
                                                                'bg-light border'}" style="max-width:70%">
                                            ${msg.message_type === 'text' ? msg.message_text : '[Nội dung khác]'}
                                            <small class="text-muted d-block mt-1" style="font-size:12px;">
                                                ${new Date(msg.sent_at).toLocaleString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}
                                            </small>
                                        </div>
                                    `;

                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                });



        });
    </script> --}}
@endsection
