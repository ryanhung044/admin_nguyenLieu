@extends('admin.layout')
@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <h1 class="mb-4">Chỉnh sửa người dùng</h1>

    <form action="{{ route('admin.users.update', $user->id) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6">
                <!-- Tên đăng nhập -->
                <div class="mb-3">
                    <label for="name" class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $user->name) }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Họ và tên đầy đủ -->
                <div class="mb-3">
                    <label for="full_name" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name"
                        name="full_name" value="{{ old('full_name', $user->full_name) }}">
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email', $user->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Mật khẩu (bỏ trống nếu không thay đổi) -->
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu (để trống nếu không đổi)</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Số điện thoại -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                        name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <!-- Giới tính -->
                <div class="mb-3">
                    <label for="gender" class="form-label">Giới tính</label>
                    <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                        <option value="">-- Chọn giới tính --</option>
                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Ngày sinh -->
                <div class="mb-3">
                    <label for="birthday" class="form-label">Ngày sinh</label>
                    <input type="date" class="form-control @error('birthday') is-invalid @enderror" id="birthday"
                        name="birthday" value="{{ old('birthday', $user->birthday) }}">
                    @error('birthday')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Địa chỉ -->
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                        name="address" value="{{ old('address', $user->address) }}">
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Số dư -->
                <div class="mb-3">
                    <label for="balance" class="form-label">Số dư tài khoản</label>
                    <input type="number" class="form-control @error('balance') is-invalid @enderror" id="balance"
                        name="balance" value="{{ old('balance', $user->balance) }}">
                    @error('balance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Vai trò -->
                <div class="mb-3">
                    <label for="role" class="form-label">Vai trò</label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Người dùng</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Avatar -->
                <div class="mb-3">
                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar"
                        name="avatar">
                    @error('avatar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if ($user->avatar)
                        <div class="mt-2">
                        @if (Str::startsWith($user->avatar, ['http://', 'https://']))
                            <img src="{{ $user->avatar }}" alt="Avatar" width="100" class="img-thumbnail">
                        @else
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" width="100" class="img-thumbnail">
                        @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật người dùng</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
@endsection
