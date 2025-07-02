{{-- @extends('admin.layout')
@section('content')
<h1>Danh sách danh mục</h1>
<a class="btn btn-success mb-2" href="{{ route('admin.product-categories.create') }}">Thêm danh mục</a>

<table class="table" border="1" cellpadding="5">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục cha</th>
            <th>Ảnh</th>
            <th>Thứ tự</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $cat)
        <tr>
            <td>{{ $cat->id }}</td>
            <td>{{ $cat->name }}</td>
            <td>{{ $cat->slug }}</td>
            <td>{{ $cat->parent->name ?? '-' }}</td>
            <td><img src="{{ $cat->image }}" width="50"></td>
            <td>{{ $cat->sort_order }}</td>
            <td>
                <a href="{{ route('admin.product-categories.edit', $cat->id) }}">Sửa</a> |
                <form action="{{ route('admin.product-categories.destroy', $cat->id) }}" method="POST" style="display:inline-block">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Xóa?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection --}}

@extends('admin.layout')

@section('content')
<h1 class="mb-4">Danh sách danh mục</h1>

<!-- Thêm danh mục -->
<a href="{{ route('admin.product-categories.create') }}" class="btn btn-primary mb-3">Thêm danh mục</a>

<!-- Bảng danh mục -->
<table id="categoryTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục cha</th>
            <th>Ảnh</th>
            <th>Thứ tự</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($categories as $cat)
        <tr>
            <td>{{ $cat->id }}</td>
            <td>{{ $cat->name }}</td>
            <td>{{ $cat->slug }}</td>
            <td>{{ $cat->parent->name ?? '-' }}</td>
            <td><img src="{{ asset('storage/' . $cat->image) }}" width="50" alt="Ảnh danh mục"></td>
            <td>{{ $cat->sort_order }}</td>
            <td>
                <a href="{{ route('admin.product-categories.edit', $cat->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('admin.product-categories.destroy', $cat->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    $(document).ready(function () {
        $('#categoryTable').DataTable({
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
@endsection