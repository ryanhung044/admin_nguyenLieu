@extends('admin.layout')

@section('content')
<h1 class="mb-4">Danh sách sản phẩm</h1>

<!-- Thêm sản phẩm -->
<a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Thêm sản phẩm</a>

<!-- Bảng sản phẩm -->
<table id="productTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục</th>
            <th>Ảnh đại diện</th>
            <th>Giá</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->slug }}</td>
            <td>{{  $product->category->name ?? '-' }}</td>
            <td><img src="{{ asset('storage/' . $product->thumbnail) }}" width="50" alt="Ảnh sản phẩm"></td>
            <td>{{ number_format($product->sale_price, 0, ',', '.') }} VND</td>
            <td>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $products->links('pagination::bootstrap-5') }}

<script>
    $(document).ready(function () {
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
@endsection
