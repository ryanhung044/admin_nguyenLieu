@extends('admin.layout')

@section('content')
    <div>
        <h1>Thêm mới Banner</h1>

        <!-- Form thêm mới banner -->
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="title">Tiêu đề</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>

            <div class="form-group">
                <label for="image">Hình ảnh Banner</label>
                <input type="file" id="image" name="image" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="link">Đường dẫn</label>
                <input type="url" id="link" name="link" class="form-control" value="{{ old('link') }}"
                    placeholder="https://...">
            </div>

            <div class="form-group">
                <label for="position">Vị trí hiển thị</label>
                {{-- <input type="text" id="position" name="position" class="form-control" value="{{ old('position') }}"
                    required> --}}
                <select id="position" name="position" class="form-control" required>
                    <option value="1" {{ old('position', 1) == 1 ? 'selected' : '' }}>Banner giữa màn hình trang chủ
                    </option>
                    <option value="2" {{ old('position') == 2 ? 'selected' : '' }}>Menu giữa màn hình trang chủ
                    </option>
                    <option value="3" {{ old('position') == 3 ? 'selected' : '' }}>Banner đại lý
                    </option>
                </select>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class=" col-md-6">
                        <label for="start_date">Ngày bắt đầu</label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control"
                            value="{{ old('start_date') }}">
                    </div>

                    <div class=" col-md-6">
                        <label for="end_date">Ngày kết thúc</label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control"
                            value="{{ old('end_date') }}">
                    </div>
                </div>

            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" class="form-control">
                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Kích hoạt</option>
                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Tắt</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Lưu banner</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
@endsection
