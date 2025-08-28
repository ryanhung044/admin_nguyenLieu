@extends('admin.layout')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Danh sách tin nhắn</h1>

    <!-- Flash message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Conversation ID</th>
                        <th>Người gửi</th>
                        <th>Nội dung</th>
                        <th>Thời gian</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $index => $msg)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $msg->conversation_id }}</td>
                            <td>{{ $msg->sender_id }}</td>
                            <td>{{ Str::limit($msg->content, 50) }}</td>
                            <td>{{ $msg->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.messages.show', $msg->id) }}" class="btn btn-sm btn-info">
                                    Xem
                                </a>
                                <form action="{{ route('admin.messages.destroy', $msg->id) }}" 
                                      method="POST" 
                                      style="display:inline-block"
                                      onsubmit="return confirm('Xóa tin nhắn này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có tin nhắn nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Phân trang -->
            <div class="mt-3">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
