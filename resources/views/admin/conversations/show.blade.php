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
        <p><strong>External ID:</strong> {{ $conversation->external_id }}</p>
        <p><strong>Tin nhắn cuối:</strong> {{ $conversation->last_message }}</p>
        <p><strong>Thời gian:</strong> 
            {{ $conversation->last_time ? \Carbon\Carbon::parse($conversation->last_time)->format('d/m/Y H:i') : '' }}
        </p>

        <a href="{{ route('admin.conversations.index') }}" class="btn btn-secondary btn-sm">
            ← Quay lại danh sách
        </a>
    </div>

    <!-- Cột bên phải: Khung chat -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Hội thoại với {{ $conversation->external_id }}</h5>
            </div>
            <div class="card-body" style="height: 500px; overflow-y: auto; background: #f9f9f9;">
                @foreach ($messages as $msg)
                    <div class="d-flex mb-3 {{ $msg->sender_type == 'admin' ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="p-2 rounded 
                            {{ $msg->sender_type == 'admin' ? 'bg-primary text-white' : 'bg-light border' }}"
                            style="max-width: 70%;">
                            
                            {{-- Hiển thị tùy theo loại message --}}
                            @if ($msg->message_type === 'text')
                                <div>{{ $msg->message_text }}</div>
                            
                            @elseif ($msg->message_type === 'image')
                                <div>
                                    <img src="{{ $msg->message_text }}" alt="image" class="img-fluid rounded">
                                </div>
                            
                            @elseif ($msg->message_type === 'sticker')
                                <div>[Sticker ID: {{ $msg->message_text }}]</div>
                            
                            @elseif ($msg->message_type === 'event')
                                <div class="text-muted fst-italic">{{ $msg->message_text }}</div>
                            
                            @else
                                <div>[Không xác định]</div>
                            @endif

                            <small class="text-muted d-block mt-1" style="font-size: 12px;">
                                {{ \Carbon\Carbon::parse($msg->created_at)->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer">
                <form action="{{ route('admin.conversations.send', $conversation->id) }}" method="POST">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="content" class="form-control" placeholder="Nhập tin nhắn..." required>
                        <button type="submit" class="btn btn-primary">Gửi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
