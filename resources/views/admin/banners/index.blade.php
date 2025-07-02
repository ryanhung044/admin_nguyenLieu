@extends('admin.layout')

@section('content')
    <h1 class="mb-4">Danh sách tài khoản</h1>

    <!-- Thêm sản phẩm -->
    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mb-3">Thêm banner</a>

    <!-- Bảng sản phẩm -->
    <table id="bannerTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Hình ảnh</th>
                <th>Tiêu đề</th>
                <th>Link</th>
                <th>Vị trí</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($banners as $banner)
                <tr>
                    <td>{{ $banner->id }}</td>
                    <td>
                        @if ($banner->image)
                            <img src="{{ asset('storage/' . $banner->image) }}" width="80" alt="Banner">
                        @else
                            <span class="text-muted">Không có ảnh</span>
                        @endif
                    </td>
                    <td>{{ $banner->title }}</td>
                    <td><a href="{{ $banner->link }}" target="_blank">{{ $banner->link }}</a></td>
                    <td>{{ $banner->position }}</td>
                    <td>{{ $banner->start_date ? \Carbon\Carbon::parse($banner->start_date)->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td>{{ $banner->end_date ? \Carbon\Carbon::parse($banner->end_date)->format('d/m/Y H:i') : '-' }}</td>
                    <td>
                        <form action="{{ route('admin.banners.toggleStatus', $banner->id) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="badge fs-5 {{ $banner->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $banner->status ? 'Kích hoạt' : 'Tắt' }}
                            </button>
                        </form>

                    </td>
                    <td>
                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
                            style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc muốn xóa banner này không?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
