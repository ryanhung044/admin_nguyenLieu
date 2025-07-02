@extends('admin.layout')

@section('content')
<h1 class="mb-4">Sửa danh mục</h1>

<!-- Form sửa danh mục -->
<form action="{{ route('admin.product-categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') <!-- Để sử dụng PUT request cho việc cập nhật -->
    
    <div class="mb-3">
        <label for="name" class="form-label">Tên danh mục</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="slug" class="form-label">Slug</label>
        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category->slug) }}">
        @error('slug')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="parent_id" class="form-label">Danh mục cha</label>
        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
            <option value="">Chọn danh mục cha</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('parent_id', $category->parent_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        @error('parent_id')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Ảnh danh mục</label>
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image">
        @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($category->image)
        <div class="mt-2">
            <img src="{{ asset('storage/' . $category->image) }}" width="100" alt="Ảnh hiện tại">
        </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="sort_order" class="form-label">Thứ tự</label>
        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}">
        @error('sort_order')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-success">Cập nhật danh mục</button>
    <a href="{{ route('admin.product-categories.index') }}" class="btn btn-secondary">Quay lại</a>
</form>
@endsection
