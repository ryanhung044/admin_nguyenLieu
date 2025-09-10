{{-- @extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card" style="height: 80vh">
                <div class="card-header">
                    <h5 class="mb-0"><img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}"
                            alt="avatar" class="rounded-circle" width="50" height="50">
                        {{ $conversation?->user?->full_name }}</h5>
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

                                @if ($msg->message_type === 'text')
                                    <div>{{ $msg->message_text }}</div>
                                @elseif ($msg->message_type === 'image')
                                    <div><img style="max-width: 200px" src="{{ $msg->message_text }}" alt="image"
                                            class="img-fluid rounded"></div>
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
        <div class="col-md-4 border-end">
            <h4 class="mb-3">Thông tin hội thoại</h4>
            <p><strong>Platform:</strong>
                <span class="badge bg-{{ $conversation->platform == 'zalo' ? 'info' : 'primary' }}">
                    {{ ucfirst($conversation->platform) }}
                </span>
            </p>

            <h4><img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}" alt="avatar"
                    class="rounded-circle" width="50" height="50"> {{ $conversation?->user?->name }}</h4>
            <p><strong>Thời gian:</strong>
                {{ $conversation->last_time ? \Carbon\Carbon::parse($conversation->last_time)->format('d/m/Y H:i') : '' }}
            </p>

            <a href="{{ route('admin.conversations.index') }}" class="btn btn-secondary btn-sm">
                ← Quay lại danh sách
            </a>
        </div>
    </div>


    <script>
        document.getElementById('sendBtn').addEventListener('click', async () => {
            const type = 'text'; // hoặc 'image', 'file', ...
            const content = document.getElementById('messageInput').value;

            const conversationId = @json($conversation->id);

            const res = await fetch(`/api/conversations/${conversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type,
                    content
                })
            });

            const data = await res.json();
            if (data.success) {
                document.getElementById('messageInput').value = '';
            }
        });

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
                                `<div><img style="max-width: 200px" src="${msg.message_text}" alt="image" class="img-fluid rounded"></div>`;
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
@endsection --}}


@extends('admin.layout')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card" style="height: 80vh; display:flex; flex-direction:column">
                <div class="card-header d-flex align-items-center">
                    <img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}" alt="avatar"
                        class="rounded-circle me-2" width="50" height="50">
                    <h5 class="mb-0">{{ $conversation?->user?->full_name ?? 'Khách hàng' }}</h5>
                </div>

                <div id="chat-box" class="card-body flex-grow-1" style="overflow-y:auto; background:#f9f9f9;">
                    @foreach ($messages as $msg)
                        <div
                            class="d-flex mb-3 @if ($msg->sender_type == 'admin') justify-content-end
                                             @elseif($msg->sender_type == 'system') justify-content-center
                                             @else justify-content-start @endif">
                            <div class="p-2 rounded @if ($msg->sender_type == 'admin') bg-primary text-white
                                                   @elseif($msg->sender_type == 'system') bg-light text-muted fst-italic
                                                   @else bg-light border @endif"
                                style="max-width:70%;">
                                @switch($msg->message_type)
                                    @case('text')
                                        <div>{{ $msg->message_text }}</div>
                                    @break

                                    @case('image')
                                        <div><img src="{{ $msg->message_text }}" style="max-width:200px" class="img-fluid rounded">
                                        </div>
                                    @break

                                    @case('video')
                                        <div><video src="{{ $msg->message_text }}" controls style="max-width:200px"></video></div>
                                    @break

                                    @case('file')
                                        <div><a href="{{ $msg->message_text }}" target="_blank">Tệp đính kèm</a></div>
                                    @break

                                    @case('sticker')
                                        <div>[Sticker ID: {{ $msg->message_text }}]</div>
                                    @break

                                    @case('event')
                                        <div class="text-muted fst-italic">{{ $msg->message_text }}</div>
                                    @break

                                    @default
                                        <div>[Không xác định]</div>
                                @endswitch
                                <small class="text-muted d-block mt-1" style="font-size:12px;">
                                    {{ \Carbon\Carbon::parse($msg->created_at)->format('H:i d/m/Y') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-footer d-flex align-items-center">
                    <input type="text" id="messageInput" class="form-control me-2" placeholder="Nhập tin nhắn...">
                    <input type="file" id="fileInput" class="me-2" style="width:200px">
                    <button class="btn btn-primary" id="sendBtn">Gửi</button>
                </div>
            </div>
        </div>

        <div class="col-md-4 border-end">
            <h4 class="mb-3">Thông tin hội thoại</h4>
            <p><strong>Platform:</strong>
                <span
                    class="badge bg-{{ $conversation->platform == 'zalo' ? 'info' : 'primary' }}">{{ ucfirst($conversation->platform) }}</span>
            </p>
            <h4><img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}"
                    class="rounded-circle me-2" width="50" height="50">
                {{ $conversation?->user?->name ?? 'Khách hàng' }}
            </h4>
            <p><strong>Thời gian:</strong>
                {{ $conversation->last_time ? \Carbon\Carbon::parse($conversation->last_time)->format('d/m/Y H:i') : '' }}</p>
            <a href="{{ route('admin.conversations.index') }}" class="btn btn-secondary btn-sm">← Quay lại danh sách</a>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chatBox = document.getElementById("chat-box");
            const messageInput = document.getElementById("messageInput");
            const sendBtn = document.getElementById("sendBtn");
            const fileInput = document.getElementById("fileInput");
            const conversationId = @json($conversation->id);

            if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;

            sendBtn.addEventListener('click', async () => {
                let type = 'text';
                let content = messageInput.value.trim();

                const file = fileInput.files[0];
                const formData = new FormData();
                formData.append('type', type);
                formData.append('content', content);

                if (file) {
                    
                    const mime = file.type;
                    if (mime.startsWith('image/')) type = 'image';
                    else if (mime.startsWith('video/')) type = 'video';
                    else type = 'file';
                    formData.set('type', type);
                    formData.set('content', file);
                }

                
                const res = await fetch(`/api/conversations/${conversationId}/messages`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    messageInput.value = '';
                    fileInput.value = '';
                }
            });

            // Realtime
            window.Echo.channel(`conversation.${conversationId}`)
                .listen('.MessageCreated', (e) => {
                    const msg = e.message;
                    if (!msg) return;

                    const div = document.createElement("div");
                    div.className = "d-flex mb-3 " +
                        (msg.sender_type === 'admin' ? "justify-content-end" : msg.sender_type === 'system' ?
                            "justify-content-center" : "justify-content-start");

                    let contentHtml = '';
                    switch (msg.message_type) {
                        case 'text':
                            contentHtml = `<div>${msg.message_text}</div>`;
                            break;
                        case 'image':
                            contentHtml =
                                `<div><img src="${msg.message_text}" style="max-width:200px" class="img-fluid rounded"></div>`;
                            break;
                        case 'video':
                            contentHtml =
                                `<div><video src="${msg.message_text}" controls style="max-width:200px"></video></div>`;
                            break;
                        case 'file':
                            contentHtml =
                                `<div><a href="${msg.message_text}" target="_blank">Tệp đính kèm</a></div>`;
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

                    const formattedTime = new Date(msg.sent_at).toLocaleString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    div.innerHTML = `<div class="p-2 rounded ${msg.sender_type==='admin'?'bg-primary text-white':msg.sender_type==='system'?'bg-light text-muted fst-italic':'bg-light border'}" style="max-width:70%">
                ${contentHtml}
                <small class="text-muted d-block mt-1" style="font-size:12px;">${formattedTime}</small>
            </div>`;

                    chatBox.appendChild(div);
                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        });
    </script>
@endsection
