@extends('admin.layout')

@section('content')
<h1>Quản lý cấu hình hoa hồng</h1>
<a href="{{ route('admin.commissions.create') }}" class="btn btn-primary mb-3">Thêm cấu hình</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Danh mục</th>
            <th>Cấp độ</th>
            <th>Phần trăm (%)</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($commissions as $item)
            <tr>
                <td>{{ $item->category->name ?? 'Không rõ' }}</td>
                <td>F{{ $item->level }}</td>
                <td>{{ $item->percentage }}</td>
                <td>
                    <a href="{{ route('admin.commissions.edit', $item->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('admin.commissions.destroy', $item->id) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Xóa cấu hình này?')" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
