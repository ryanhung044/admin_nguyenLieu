@extends('admin.layout')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-4">📦 Import giá và biến thể sản phẩm từ file Excel</h3>

            <form action="{{ route('admin.import.price') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Chọn file Excel (.xlsx)</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
                </div>

                <button type="submit" class="btn btn-primary">🚀 Tải lên & Import</button>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-4">📦 Import sản phẩm từ file Excel</h3>

            <form action="{{ route('admin.import-products.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Chọn file Excel (.xlsx)</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".xlsx,.xls" required>
                </div>

                <button type="submit" class="btn btn-primary">🚀 Tải lên & Import</button>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="mb-4">📦 Import danh mục từ file Excel</h3>

            <form action="{{ route('admin.categories.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Chọn file Excel danh mục:</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Import</button>
            </form>
        </div>
    </div>
@endsection
