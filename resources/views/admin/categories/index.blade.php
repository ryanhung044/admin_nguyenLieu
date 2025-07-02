@extends('admin.layout')

@section('content')
    <h1 class="mb-4">Danh sách danh mục</h1>

    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Thêm danh mục</a>

    <table id="categoryTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Slug</th>
                <th>Danh mục cha</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $cat)
                <tr>
                    <td>{{ $cat->id }}</td>
                    <td>{{ $cat->title }}</td>
                    <td>{{ $cat->slug }}</td>
                    <td>{{ $cat->parent->title ?? '-' }}</td>
                    <td>{{ $cat->description }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST"
                            style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
