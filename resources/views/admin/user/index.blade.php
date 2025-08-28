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
                            <img src="https://static.vecteezy.com/system/resources/previews/009/292/244/non_2x/default-avatar-icon-of-social-media-user-vector.jpg"
                                width="50" alt="">
                        @endif
                    </td>
                    <td>
                        <a href="#" class="user-detail-link" data-id="{{ $user->id }}">
                            {{ $user->full_name }}
                        </a>
                    </td>

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
    <!-- Modal hiển thị thông tin chi tiết -->
    <div class="modal fade" id="userDetailModal" tabindex="-1" role="dialog" aria-labelledby="userDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông tin khách hàng</h5>
                </div>
                <div class="modal-body">
                    <!-- Nội dung được load động -->
                    <div id="userDetailContent">
                        <p class="text-muted">Đang tải...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.user-detail-link').click(function(e) {
                e.preventDefault();

                var userId = $(this).data('id');
                // return console.log(userId);

                $('#userDetailContent').html('<p class="text-muted">Đang tải...</p>');
                $('#userDetailModal').modal('show');

                $.ajax({
                    url: '/admin/users/' + userId + '/detail',
                    type: 'GET',
                    success: function(data) {
                        $('#userDetailContent').html(data);
                    },
                    error: function() {
                        $('#userDetailContent').html(
                            '<p class="text-danger">Không thể tải dữ liệu khách hàng.</p>');
                    }
                });
            });
        });
    </script>
@endsection
