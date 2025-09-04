@extends('admin.layout')

@section('title', 'Danh sách hội thoại')

@section('content')
    <div class="container">
        <h1 class="mb-4">Danh sách hội thoại</h1>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nền tảng</th>
                    <th>Người dùng</th>
                    <th>Hình ảnh</th>
                    <th>Tin nhắn cuối</th>
                    <th>Thời gian cuối</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($conversations as $index => $conv)
                    <tr>
                        <td>{{ $conversations->firstItem() + $index }}</td>
                        <td>{{ $conv->platform }}</td>
                        <td>{{ $conversation->user->full_name ?? 'Ẩn danh' }}</td>
                        <td>
                            <img src="{{ $conversation->user->avatar ?? asset('images/default-avatar.png') }}"
                                alt="avatar" class="rounded-circle" width="40" height="40">
                        {{ $conversation->user->name ?? 'Khách hàng' }}
                        </td>
                        <td>{{ $conv->last_message }}</td>
                        <td>
                            {{ $conv->last_time ? \Carbon\Carbon::parse($conv->last_time)->format('d/m/Y H:i') : '' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.conversations.show', $conv->id) }}" class="btn btn-sm btn-primary">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Không có hội thoại nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div>
            {{ $conversations->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
