<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>

    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-container {
            max-width: 400px;
            margin: 65px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="register-container">
            <h3 class="text-center">Đăng nhập</h3>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        placeholder="Nhập số điện thoại" value="{{ old('phone') }}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                    <div class="password-container d-flex">
                        <input type="password" value="{{ old('password') }}" class="form-control" name="password"
                            id="password" placeholder="Nhập mật khẩu" required>
                        <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="password" class="form-label">Ghi nhớ tài khoản </label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                <p class="text-center mt-3">Bạn chưa có tài khoản? <a href="signup"><span>Đăng ký ngay</span></a></p>

            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Lấy dữ liệu từ LocalStorage và điền vào form nếu có
            if (localStorage.getItem("remember") === "true") {
                document.getElementById("phone").value = localStorage.getItem("phone") || "";
                document.getElementById("password").value = localStorage.getItem("password") || "";
                document.getElementById("remember").checked = true;
            }

            // Xử lý sự kiện khi click vào mắt mật khẩu
            document.querySelectorAll('.toggle-password').forEach(item => {
                item.addEventListener('click', function() {
                    let input = this.previousElementSibling;
                    if (input.type === "password") {
                        input.type = "text";
                        this.classList.remove("bi-eye-slash");
                        this.classList.add("bi-eye");
                    } else {
                        input.type = "password";
                        this.classList.remove("bi-eye");
                        this.classList.add("bi-eye-slash");
                    }
                });
            });

            // Xử lý sự kiện khi submit form
            document.querySelector("form").addEventListener("submit", function(event) {
                event.preventDefault(); // Ngăn chặn form gửi đi để kiểm tra lưu dữ liệu

                let phone = document.getElementById("phone").value;
                let password = document.getElementById("password").value;
                let remember = document.getElementById("remember").checked;
                if (remember) {
                    // Lưu vào LocalStorage nếu người dùng chọn "Ghi nhớ tài khoản"
                    localStorage.setItem("phone", phone);
                    localStorage.setItem("password", password);
                    localStorage.setItem("remember", "true");
                } else {
                    // Xóa dữ liệu khỏi LocalStorage nếu không chọn "Ghi nhớ tài khoản"
                    localStorage.removeItem("phone");
                    localStorage.removeItem("password");
                    localStorage.removeItem("remember");
                }

                // alert("Đăng nhập thành công! (Dữ liệu đã lưu vào LocalStorage)");
                this.submit(); // Nếu muốn form gửi đi, bỏ comment dòng này
            });
        });
    </script>

</body>

</html>

{{-- @extends('layout')

@section('title', 'Trang chủ')
@section('content')
    <style>
        body {
            background-color: #f8f9fa;
        }

        .register-container {
            max-width: 400px;
            margin: 65px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
    <div class="container">
        <div class="register-container">
            <h3 class="text-center">Đăng nhập</h3>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        placeholder="Nhập số điện thoại" value="{{ old('phone') }}" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                    <div class="password-container d-flex">
                        <input type="password" value="{{ old('password') }}" class="form-control" name="password"
                            id="password" placeholder="Nhập mật khẩu" required>
                        <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <div class="mb-3">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="password" class="form-label">Ghi nhớ tài khoản </label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                <p class="text-center mt-3">Bạn chưa có tài khoản? <a href="signup"><span>Đăng ký ngay</span></a></p>

            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Lấy dữ liệu từ LocalStorage và điền vào form nếu có
            if (localStorage.getItem("remember") === "true") {
                document.getElementById("phone").value = localStorage.getItem("phone") || "";
                document.getElementById("password").value = localStorage.getItem("password") || "";
                document.getElementById("remember").checked = true;
            }

            // Xử lý sự kiện khi click vào mắt mật khẩu
            document.querySelectorAll('.toggle-password').forEach(item => {
                item.addEventListener('click', function() {
                    let input = this.previousElementSibling;
                    if (input.type === "password") {
                        input.type = "text";
                        this.classList.remove("bi-eye-slash");
                        this.classList.add("bi-eye");
                    } else {
                        input.type = "password";
                        this.classList.remove("bi-eye");
                        this.classList.add("bi-eye-slash");
                    }
                });
            });

            // Xử lý sự kiện khi submit form
            document.querySelector("form").addEventListener("submit", function(event) {
                event.preventDefault(); // Ngăn chặn form gửi đi để kiểm tra lưu dữ liệu

                let phone = document.getElementById("phone").value;
                let password = document.getElementById("password").value;
                let remember = document.getElementById("remember").checked;
                if (remember) {
                    // Lưu vào LocalStorage nếu người dùng chọn "Ghi nhớ tài khoản"
                    localStorage.setItem("phone", phone);
                    localStorage.setItem("password", password);
                    localStorage.setItem("remember", "true");
                } else {
                    // Xóa dữ liệu khỏi LocalStorage nếu không chọn "Ghi nhớ tài khoản"
                    localStorage.removeItem("phone");
                    localStorage.removeItem("password");
                    localStorage.removeItem("remember");
                }

                // alert("Đăng nhập thành công! (Dữ liệu đã lưu vào LocalStorage)");
                this.submit(); // Nếu muốn form gửi đi, bỏ comment dòng này
            });
        });
    </script>
@endsection --}}
