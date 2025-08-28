@extends('admin.layout')

@section('content')
<div >
    <h1 class="mb-4">Hội thoại với {{ $conversation->external_id }} ({{ ucfirst($conversation->platform) }})</h1>

    <div class="card">
        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
            @foreach ($conversation->messages as $msg)
                <div class="mb-3">
                    <strong class="{{ $msg->sender_type == 'admin' ? 'text-primary' : 'text-dark' }}">
                        {{ $msg->sender_type == 'admin' ? 'Admin' : 'User' }}:
                    </strong>
                    @if($msg->message_type === 'text')
                        {{ $msg->message_text }}
                    @elseif($msg->message_type === 'image')
                        <img src="{{ $msg->message_data['url'] ?? '' }}" class="img-thumbnail" width="200">
                    @endif
                    <small class="text-muted d-block">{{ $msg->sent_at?->format('d/m/Y H:i') }}</small>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
