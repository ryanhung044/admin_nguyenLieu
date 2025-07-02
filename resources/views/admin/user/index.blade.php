@extends('admin.layout')
@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <h1 class="mb-4">Danh sách tài khoản</h1>

    <!-- Thêm sản phẩm -->
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Thêm tài khoản</a>

    <!-- Bảng sản phẩm -->
    <table id="productTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Ảnh đại diện</th>
                <th>Tên</th>
                <th>Số điện thoại</th>
                <th>Giới tính</th>
                <th>Số dư tài khoản</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>

                        @if ($user->avatar)
                           @if (Str::startsWith($user->avatar, ['http://', 'https://']))
                                <img src="{{ $user->avatar }}" width="50" alt="">
                            @else
                                <img src="{{ asset('storage/' . $user->avatar) }}" width="50" alt="">
                            @endif
                        @else
                            <img src="https://static.vecteezy.com/system/resources/previews/009/292/244/non_2x/default-avatar-icon-of-social-media-user-vector.jpg" width="50" alt="">
                        @endif
                    </td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>
                        @if ($user->gender === 'male')
                            Nam
                        @elseif ($user->gender === 'female')
                            Nữ
                        @else
                            Khác
                        @endif
                    </td>
                    <td>{{ number_format($user->balance, 0, ',', '.') }} VND</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                            style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#productTable').DataTable({
                "language": {
                    "search": "Tìm kiếm:",
                    "lengthMenu": "Hiển thị _MENU_ mục",
                    "info": "Hiển thị _START_ đến _END_ trong _TOTAL_ mục",
                    "paginate": {
                        "first": "Đầu",
                        "last": "Cuối",
                        "next": "Tiếp",
                        "previous": "Trước"
                    },
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "infoEmpty": "Không có dữ liệu",
                    "infoFiltered": "(lọc từ _MAX_ mục)"
                }
            });
        });
    </script>
@endpush
