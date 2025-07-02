@extends('admin.layout')

@section('content')
<h1 class="mb-4">Thêm hoa hồng</h1>

<form action="{{ route('admin.commissions.store') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label for="category_id" class="form-label">Danh mục</label>
        <select name="category_id" id="category_id" class="form-select" required>
            <option value="">-- Chọn danh mục --</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="level" class="form-label">Cấp độ (F1, F2, ...)</label>
        <input type="number" name="level" id="level" class="form-control" min="1" required>
    </div>

    <div class="mb-3">
        <label for="percentage" class="form-label">Phần trăm (%)</label>
        <input type="number" name="percentage" id="percentage" class="form-control" min="0" max="100" step="0.01" required>
    </div>

    <button type="submit" class="btn btn-success">Lưu</button>
    <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
@endsection
