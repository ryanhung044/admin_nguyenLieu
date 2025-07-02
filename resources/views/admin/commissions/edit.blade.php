@extends('admin.layout')

@section('content')
<h1 class="mb-4">Chỉnh sửa hoa hồng</h1>

<form action="{{ route('admin.commissions.update', $commission->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="category_id" class="form-label">Danh mục</label>
        <select name="category_id" id="category_id" class="form-select" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ $commission->category_id == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="level" class="form-label">Cấp độ</label>
        <input type="number" name="level" id="level" class="form-control" value="{{ $commission->level }}" min="1" required>
    </div>

    <div class="mb-3">
        <label for="percentage" class="form-label">Phần trăm (%)</label>
        <input type="number" name="percentage" id="percentage" class="form-control" value="{{ $commission->percentage }}" step="0.01" min="0" max="100" required>
    </div>

    <button type="submit" class="btn btn-primary">Cập nhật</button>
    <a href="{{ route('admin.commissions.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
@endsection
