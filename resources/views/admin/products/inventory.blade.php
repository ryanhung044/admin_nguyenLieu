@extends('admin.layout')

@section('content')
<style>
    .form-select, .form-control, .btn {
        height: 45px !important;
    }
</style>

    <h1 class="mb-4">Quản lý tồn kho của sản phẩm</h1>

    <a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Thêm sản phẩm</a>

    <!-- 🔍 Bộ lọc -->
    <form method="GET" action="{{ route('admin.inventory.indexStock') }}" class="row g-2 align-items-end mb-3">
        <div class="col-md-4">
            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                placeholder="Tìm theo tên...">
        </div>
        <div class="col-md-2">
            <select name="category_id" class="form-select">
                <option value="">-- Tất cả danh mục --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="stock_status" class="form-select">
                <option value="">-- Tất cả tồn kho --</option>
                <option value="in" {{ request('stock_status') == 'in' ? 'selected' : '' }}>Còn hàng</option>
                <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Hết hàng</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="per_page" class="form-select" onchange="this.form.submit()">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / trang</option>
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 / trang</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / trang</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 / trang</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            <a href="{{ route('admin.inventory.indexStock') }}" class="btn btn-outline-secondary text-nowrap"><i class="fas fa-trash"></i></a>
        </div>
    </form>

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

    {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
@endsection
