@extends('admin.layout')

@section('content')
    <h1 class="mb-4">Quản lý tồn kho của sản phẩm</h1>

    <!-- Thêm sản phẩm -->
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Thêm sản phẩm</a>

    <!-- Bảng sản phẩm -->
    <table id="productTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Ảnh đại diện</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr class="{{ $product->stock == 0 ? 'table-danger' : '' }}">
                    <td>{{ $product->id }}</td>
                    <td>{{ $product->name }}</td>
                    <td><img src="{{ asset('storage/' . $product->thumbnail) }}" width="50"></td>
                    <td>{{ number_format($product->sale_price, 0, ',', '.') }} VND</td>
                    <td>
                        <form action="{{ route('admin.inventory.updateStock', $product->id) }}" method="POST"
                            class="d-flex gap-1">
                            @csrf
                            @method('PUT')
                            <input type="number" name="stock" value="{{ $product->stock }}" min="0"
                                class="form-control form-control-sm" style="width: 80px;">
                            <button type="submit" class="btn btn-sm btn-success">Lưu</button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">Cập
                            nhật</a>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
    {{ $products->links('pagination::bootstrap-5') }}

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
