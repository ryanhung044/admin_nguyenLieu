@extends('admin.layout')

@section('content')
    <h1 class="mb-4">Yêu cầu rút tiền</h1>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Người yêu cầu</th>
                <th>Số tiền</th>
                <th>Trạng thái</th>
                <th>Chủ tài khoản</th>
                <th>Số tài khoản</th>
                <th>Ngân hàng</th>
                <th>Ảnh chứng từ</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($withdrawRequests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->user->name ?? 'Không có' }}</td>
                    <td>{{ number_format($request->amount, 0, ',', '.') }} VND</td>
                    <td>
                        @if ($request->status == 'pending')
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif($request->status == 'approved')
                            <span class="badge bg-success">Đã chuyển</span>
                        @else
                            <span class="badge bg-danger">Từ chối</span>
                        @endif
                    </td>
                    <td>{{ $request->bankAccount->account_name ?? 'Chưa có' }}</td>
                    <td>{{ $request->bankAccount->account_number ?? 'Chưa có' }}</td>
                    <td>{{ $request->bankAccount->bank_name ?? 'Chưa có' }}</td>
                    <td>
                        @if ($request->image)
                            <img src="{{ asset('storage/' . $request->image) }}" width="80" alt="Proof">
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($request->status == 'pending')
                            <a href="{{ route('admin.bank-accounts.edit', $request->id) }}" class="btn btn-sm btn-primary">Cập
                                nhật</a>
                        @else
                        @endif
                        {{-- <a href="{{ route('admin.bank-accounts.edit', $request->id) }}" class="btn btn-sm btn-primary">Cập
                            nhật</a> --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $withdrawRequests->links('pagination::bootstrap-5') }}
@endsection
