@extends('admin.layout')

@section('content')
    <div>
        <h1 class="mb-4">Danh sách hội thoại</h1>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Platform</th>
                    <th>External ID</th>
                    <th>Tin nhắn cuối</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($conversations as $conv)
                    <tr>
                        <td>
                            <span class="badge bg-{{ $conv->platform == 'zalo' ? 'info' : 'primary' }}">
                                {{ ucfirst($conv->platform) }}
                            </span>
                        </td>
                        <td>{{ $conv->external_id }}</td>
                        <td>{{ $conv->last_message }}</td>
                        <td>
                            {{ $conv->last_time ? \Carbon\Carbon::parse($conv->last_time)->format('d/m/Y H:i') : '' }}
                        </td>

                        <td>
                            <a href="{{ route('admin.conversations.show', $conv->id) }}" class="btn btn-sm btn-success">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $conversations->links() }}
    </div>
@endsection
