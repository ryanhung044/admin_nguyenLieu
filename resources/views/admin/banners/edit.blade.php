@extends('admin.layout')

@section('content')
    <div>
        <h1>Cập nhật Banner</h1>

        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Laravel sử dụng PUT cho update --}}

            <div class="form-group">
                <label for="title">Tiêu đề</label>
                <input type="text" id="title" name="title" class="form-control"
                    value="{{ old('title', $banner->title) }}" required>
            </div>

            <div class="form-group">
                <label>Hình ảnh hiện tại:</label><br>
                @if ($banner->image)
                    <img src="{{ asset('storage/' . $banner->image) }}" width="100" alt="Banner">
                @endif
            </div>

            <div class="form-group">
                <label for="image">Cập nhật hình ảnh (nếu cần)</label>
                <input type="file" id="image" name="image" class="form-control">
            </div>

            <div class="form-group">
                <label for="link">Đường dẫn</label>
                <input type="url" id="link" name="link" class="form-control"
                    value="{{ old('link', $banner->link) }}" placeholder="https://...">
            </div>

            <div class="form-group">
                <label for="position">Vị trí hiển thị</label>
                {{-- <input type="text" id="position" name="position" class="form-control"
                    value="{{ old('position', $banner->position) }}" required> --}}
                <select id="position" name="position" class="form-control" required>
                    <option value="1" {{ old('position', $banner->position) == 1 ? 'selected' : '' }}>Banner giữa màn
                        hình trang chủ
                    </option>
                    <option value="2" {{ old('position', $banner->position) == 2 ? 'selected' : '' }}>Menu giữa màn
                        hình trang chủ</option>
                    <option value="3" {{ old('position', $banner->position) == 3 ? 'selected' : '' }}>Banner đại lý
                    </option>
                </select>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="start_date">Ngày bắt đầu</label>
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control"
                            value="{{ old('start_date', \Carbon\Carbon::parse($banner->start_date)->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="end_date">Ngày kết thúc</label>
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control"
                            value="{{ old('end_date', \Carbon\Carbon::parse($banner->end_date)->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Trạng thái</label>
                <select id="status" name="status" class="form-control">
                    <option value="1" {{ old('status', $banner->status) == 1 ? 'selected' : '' }}>Kích hoạt</option>
                    <option value="0" {{ old('status', $banner->status) == 0 ? 'selected' : '' }}>Tắt</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật banner</button>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
@endsection
