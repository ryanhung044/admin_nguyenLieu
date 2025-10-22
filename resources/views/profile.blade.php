@extends('admin.layout')

@section('title', 'My Profile')

@section('content')
{{-- <div class="page-inner"> --}}
    <div class="row">
        <div class="col-md-12">
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin cá nhân</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <div class="form-group">
                                    <div class="avatar-wrapper mb-3">
                                        <div class="avatar-container" style="position: relative; display: inline-block; cursor: pointer; width: 200px; height: 200px;" onclick="document.getElementById('avatar-input').click();">
                                           <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
     alt="Avatar" class="img-fluid rounded-circle"
     style="width: 200px; height: 200px; object-fit: cover;" id="avatar-preview">

                                            <div class="avatar-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); 
                                                border-radius: 50%; opacity: 0; transition: opacity 0.3s; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa fa-camera" style="color: white; font-size: 24px;"></i>
                                            </div>
                                        </div>
                                        <input type="file" name="avatar" id="avatar-input" style="display: none;" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                </div>
                                <h4>{{ $user->full_name }}</h4>
                                
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="full_name">Tên đầy đủ</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $user->full_name }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Số điện thoại</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="address">Địa chỉ</label>
                                    <input type="text" class="form-control" id="address" name="address" value="{{ $user->address }}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="birthday">Ngày sinh</label>
                                    <input type="date" class="form-control" id="birthday" name="birthday" value="{{ $user->birthday }}">
                                </div>
                                
                                <div class="form-group">
                                    <label for="gender">Giới tính</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                        <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                        <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Khác</option>
                                    </select>
                                </div>
                                
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
{{-- </div> --}}

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const avatarContainer = document.querySelector('.avatar-container');
    const overlay = document.querySelector('.avatar-overlay');
    
    avatarContainer.addEventListener('mouseenter', function() {
        overlay.style.opacity = '1';
    });
    
    avatarContainer.addEventListener('mouseleave', function() {
        overlay.style.opacity = '0';
    });
});
</script>

@endsection