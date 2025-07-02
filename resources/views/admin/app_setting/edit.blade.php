@extends('admin.layout')

@section('content')
    <div>
        <h1>Cập nhật Thông tin Ứng dụng</h1>

        <form action="{{ route('admin.app-setting.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="app_name">Tên Ứng dụng</label>
                <input type="text" name="app_name" id="app_name" class="form-control"
                    value="{{ old('app_name', $setting->app_name) }}">
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <input type="text" name="address" id="address" class="form-control"
                    value="{{ old('address', $setting->address) }}">
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label for="latitude">Vĩ độ</label>
                    <input type="text" name="latitude" id="latitude" class="form-control"
                        value="{{ old('latitude', $setting->latitude) }}">
                </div>
                <div class="col-md-6">
                    <label for="longitude">Kinh độ</label>
                    <input type="text" name="longitude" id="longitude" class="form-control"
                        value="{{ old('longitude', $setting->longitude) }}">
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="phone">Số điện thoại</label>
                    <input type="text" name="phone" id="phone" class="form-control"
                        value="{{ old('phone', $setting->phone) }}">
                </div>

                <div class="col-md-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control"
                        value="{{ old('email', $setting->email) }}">
                </div>

                <div class="col-md-4">
                    <label for="default_color">Màu mặc định trên app</label>

                    <div class="input-group">
                        <div class="input-group">
                            <input type="text" name="default_color" id="default_color" class="form-control"
                                   placeholder="#000000"
                                   value="{{ old('default_color', $setting->default_color) }}">
                            <input type="color" id="color_picker" value="{{ old('default_color', $setting->default_color) }}"
                                   style="width: 60px;height: 100%; padding: 0; border: none; background: none;">
                        </div>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label for="description">Thông tin ứng dụng</label>
                <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $setting->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="donated">Tặng khách mới</label>
                <input type="number" name="donated" id="donated" class="form-control"
                    value="{{ old('donated', $setting->donated) }}">
            </div>

            <div class="form-group">
                <label>Logo Web hiện tại:</label><br>
                @if ($setting->logo_path)
                    <img src="{{ asset('storage/' .$setting->logo_path) }}" width="100" alt="Logo">
                @endif
                <input type="file" name="logo_path" class="form-control mt-2">
            </div>

            {{-- <div class="form-group">
                <label>Banner Ứng dụng hiện tại:</label><br>
                @if ($setting->banner_path)
                    <img src="{{ asset('storage/' . $setting->banner_path) }}" width="200" alt="Banner">
                @endif
                <input type="file" name="banner_path" class="form-control mt-2">
            </div> --}}

            <div class="form-group">
                <label>Favicon hiện tại:</label><br>
                @if ($setting->favicon_path)
                    <img src="{{ asset('storage/' .$setting->favicon_path) }}" width="100" alt="Favicon">
                @endif
                <input type="file" name="favicon_path" class="form-control mt-2">
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            {{-- <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Quay lại</a> --}}
        </form>
    </div>
    <script>
        const colorInput = document.getElementById('default_color');
        const colorPicker = document.getElementById('color_picker');
    
        // Khi chọn màu từ bảng -> cập nhật ô text
        colorPicker.addEventListener('input', function () {
            colorInput.value = this.value;
        });
    
        // Khi người dùng gõ vào input text -> cập nhật lại bảng màu
        colorInput.addEventListener('input', function () {
            if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(this.value)) {
                colorPicker.value = this.value;
            }
        });
    </script>
    
@endsection
