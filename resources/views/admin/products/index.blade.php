@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center gap-2 ">

        <h1 class="mb-4">Danh sách sản phẩm</h1>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus-square"></i> Thêm sản phẩm</a>

    </div>
    <!-- Bộ lọc -->
    {{-- <div class="row"> --}}

        {{-- <div class=" mb-3" style="overflow: auto"> --}}
        <div class="btn-group col-lg-6 mb-4 w-100" style="overflow: auto" role="group" aria-label="Lọc theo danh mục">
            @php
                $categoryFilters = ['all' => 'Tất cả'] + $categories->pluck('name', 'id')->toArray();
            @endphp
            @foreach ($categoryFilters as $key => $label)
                <a href="{{ route('admin.products.index', ['category' => $key]) }}"
                    class="btn {{ request('category', 'all') == $key ? 'btn-primary' : 'btn-outline-primary' }} me-2" style="text-wrap:nowrap">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="d-block d-md-flex justify-content-end w-100 align-items-center gap-2 col-lg-6 mb-4">
            <form method="GET" action="{{ route('admin.products.index') }}" class="d-block d-md-flex gap-2">
                {{-- Giữ lại danh mục đang chọn --}}
                <input type="hidden" name="category" value="{{ request('category', 'all') }}">

                {{-- Khoảng giá --}}
                <input type="number" name="min_price" value="{{ request('min_price') }}" class="form-control"
                    placeholder="Giá từ" min="0" style="min-width:120px">
                <input type="number" name="max_price" value="{{ request('max_price') }}" class="form-control"
                    placeholder="Giá đến" min="0" style="min-width:120px">
                <select name="per_page" class="form-select" style="min-width:100px" onchange="this.form.submit()">
                    @foreach ([10, 20, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                            {{ $size }}/trang
                        </option>
                    @endforeach
                </select>
                {{-- Ô tìm kiếm --}}
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm sản phẩm..."
                    class="form-control" style="min-width:150px">

                <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="fas fa-search"></i> Lọc
                </button>
            </form>
        </div>
        {{-- </div> --}}
    {{-- </div> --}}

    <!-- Thêm sản phẩm -->

    <!-- Bảng sản phẩm -->
    <div class="table-responsive">
    <table id="productTable" class="table table-bordered table-striped ">
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
                    <td style="min-width: 200px">{{ $product->name }}</td>
                    <td style="min-width: 200px">{{ $product->slug }}</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                    <td><img src="{{ asset('storage/' . $product->thumbnail) }}" width="50" alt="Ảnh sản phẩm">
                    </td>
                    <td>{{ number_format($product->sale_price, 0, ',', '.') }} VND</td>
                    <td>
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
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
    </div>
    {{ $products->links('pagination::bootstrap-5') }}

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
@endsection
