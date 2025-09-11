@extends('admin.layout')

@section('title', 'Danh sách hội thoại')

@section('content')
    <div>
        <h1 class="mb-4">Danh sách hội thoại</h1>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nền tảng</th>
                        <th>Người dùng</th>
                        <th>Tin nhắn cuối</th>
                        <th>Thời gian cuối</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($conversations as $index => $conv)
                        <tr data-id="{{ $conv->id }}">
                            <td>{{ $conversations->firstItem() + $index }}</td>
                            <td>{{ $conv->platform }}</td>
                            <td>
                                <img src="{{ $conv->user->avatar ?? asset('images/default-avatar.png') }}" alt="avatar"
                                    class="rounded-circle" width="40" height="40">
                                {{ $conv->user->name ?? 'Khách hàng' }}
                            </td>
                            <td class="last-message">
                                @if ($conv->unread_count > 0)
                                    <span class="badge bg-danger ms-1">{{ $conv->unread_count }}</span>
                                @endif
                                {{ \Illuminate\Support\Str::limit($conv->last_message, 50) }}
                            </td>
                            <td class="last-time">
                                {{ $conv->last_time ? \Carbon\Carbon::parse($conv->last_time)->format('H:i d/m/Y') : '' }}
                            </td>

                            <td>
                                <a href="{{ route('admin.conversations.show', $conv->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-message"></i>
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
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tbody = document.querySelector('table tbody');

            window.Echo.channel('conversations')
                .listen('.MessageCreated', (e) => {
                    const msg = e.message;
                    if (!msg) return;

                    let row = document.querySelector(`tr[data-id='${msg.conversation_id}']`);

                    if (!row) {
                        row = document.createElement('tr');
                        row.dataset.id = msg.conversation_id;
                        const unreadBadge = msg.sender_type !== 'admin' ?
                            `<span class="badge bg-danger ms-1">1</span>` : '';

                        row.innerHTML = `
                    <td>#</td>
                    <td>${msg.conversation?.platform ?? 'N/A'}</td>
                    <td>
                        <img src="${msg.conversation?.user?.avatar ?? '/images/default-avatar.png'}" class="rounded-circle" width="40" height="40">
                        ${msg.conversation?.user?.name ?? 'Khách hàng'}
                    </td>
                    <td class="last-message">
                        ${unreadBadge}
                        ${msg.message_text?.length > 50 ? msg.message_text.substr(0,50) + '...' : msg.message_text ?? '[Không xác định]'} 
                    </td>
                    <td class="last-time">${msg.sent_at ? new Date(msg.sent_at).toLocaleString('vi-VN', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' }) : ''}</td>
                    <td>
                        <a href="/admin/conversations/${msg.conversation_id}" class="btn btn-sm btn-primary">
                                <i class="fa-solid fa-message"></i>
                        </a>
                    </td>
                `;

                        tbody.prepend(row);
                    } else {
                        const lastMsgTd = row.querySelector('.last-message');
                        if (lastMsgTd) {
                            let badgeHTML = '';
                            if (msg.sender_type !== 'admin') {
                                const oldCount = parseInt(lastMsgTd.querySelector('.badge')?.textContent || 0);
                                badgeHTML = `<span class="badge bg-danger ms-1">${oldCount + 1}</span>`;
                            }

                            lastMsgTd.innerHTML = `
                        ${msg.message_text?.length > 50 ? msg.message_text.substr(0,50) + '...' : msg.message_text ?? '[Không xác định]'}
                        ${badgeHTML}
                    `;
                        }

                        const lastTimeTd = row.querySelector('.last-time');
                        if (lastTimeTd) {
                            const sentAt = msg.sent_at ? new Date(msg.sent_at) : new Date();
                            lastTimeTd.textContent = sentAt.toLocaleString('vi-VN', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    }

                    row.classList.add('table-warning');
                    setTimeout(() => row.classList.remove('table-warning'), 2000);
                });
        });
    </script>




@endsection
