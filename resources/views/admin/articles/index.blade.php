@extends('admin.layout')

@section('content')
<h1 class="mb-4">Danh sách bài viết</h1>

<!-- Thêm sản phẩm -->
<a href="{{ route('admin.articles.create') }}" class="btn btn-primary mb-3">Thêm bài viết</a>

<!-- Bảng sản phẩm -->
<table id="productTable" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Tên</th>
            <th>Slug</th>
            <th>Danh mục</th>
            <th>Ảnh đại diện</th>
            <th>Thời gian đăng</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $article)
        <tr>
            <td>{{ $article->id }}</td>
            <td>{{ $article->title }}</td>
            <td>{{ $article->slug }}</td>
            <td>{{  $article->category->title ?? '-' }}</td>
            <td><img src="{{ asset('storage/' . $article->image) }}" width="50" alt="Ảnh sản phẩm"></td>
            <td>{{ $article->published_at ?? ''}}</td>
            <td>{{ $article->created_at ?? ''}}</td>
            <td>
                <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

