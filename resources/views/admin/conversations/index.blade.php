@extends('admin.layout')

@section('content')
<div class="chat-container">
    <h3>Hội thoại với: {{ $conversation->external_id }}</h3>
    <div class="chat-box border p-3 mb-3" style="height:400px; overflow-y:auto;">
        @foreach($messages as $msg)
            <div class="mb-2 {{ $msg->sender == 'admin' ? 'text-end' : '' }}">
                <span class="badge bg-{{ $msg->sender == 'admin' ? 'success' : 'secondary' }}">
                    {{ $msg->sender }}
                </span>
                <div class="d-inline-block p-2 rounded {{ $msg->sender == 'admin' ? 'bg-success text-white' : 'bg-light' }}">
                    {{ $msg->content }}
                </div>
                <small class="d-block text-muted">{{ $msg->created_at->format('d/m/Y H:i') }}</small>
            </div>
        @endforeach
    </div>

    <form action="{{ route('admin.conversations.send', $conversation->id) }}" method="POST" class="d-flex">
        @csrf
        <input type="text" name="content" class="form-control me-2" placeholder="Nhập tin nhắn...">
        <button type="submit" class="btn btn-primary">Gửi</button>
    </form>
</div>
@endsection
