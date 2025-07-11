<?php
    use Carbon\Carbon;
    $AppSetting = \App\Models\appSetting::first();
    $notifications = \App\Models\Notification::latest()->whereDate('created_at', Carbon::today())->get(); // Lấy tất cả thông báo mới nhất
    $Countnotifications = \App\Models\Notification::whereDate('created_at', Carbon::today())->count();
    // dd($AppSetting->logo_path);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?php echo $__env->yieldContent('title', 'Quản lý cửa hàng'); ?></title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="<?php echo e(asset('storage/' . $AppSetting->favicon_path)); ?>" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="/assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
        WebFont.load({
            google: {
                families: ["Public Sans:300,400,500,600,700"]
            },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["/assets/css/fonts.min.css"],
            },
            active: function() {
                sessionStorage.fonts = true;
            },
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"></script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/assets/css/plugins.min.css" />
    <link rel="stylesheet" href="/assets/css/kaiadmin.min.css" />

    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="/assets/css/demo.css" />
    <style>
        ul {
            margin: 0;
        }

        .form-label {
            font-weight: bold
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <!-- Logo Header -->
                <div class="logo-header" data-background-color="dark">
                    <a href="/admin" class="logo">
                        <img src="<?php echo e(asset('storage/' . $AppSetting->logo_path)); ?>" alt="navbar brand"
                            class="navbar-brand" height="60" style="border-radius: 50%; " />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
                <!-- End Logo Header -->
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <li class="nav-item active">
                            <a href="<?php echo e(route('admin')); ?>" class="collapsed" aria-expanded="false">
                                <i class="fas fa-home"></i>
                                <p>Trang chủ</p>
                            </a>

                        </li>
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Cửa hàng</h4>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.app-setting.index')); ?>">
                                <i class="fas fa-info-circle"></i>
                                <p>Thông tin ứng dụng</p>
                                
                            </a>
                            <a data-bs-toggle="collapse" href="#banners">
                                <i class="far fa-images"></i>
                                <p>Quản lý banner</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="banners">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="<?php echo e(route('admin.banners.index')); ?>">
                                            <span class="sub-item">Danh sách banner</span>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                            <a href="<?php echo e(route('admin.bank-accounts.index')); ?>">
                                <i class="fas fa-money-check-alt"></i>
                                <p>Yêu cầu rút tiền</p>
                            </a>

                        </li>
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Sản phẩm</h4>
                        </li>
                        
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#productCategory">
                                <i class="fas fa-folder-open"></i>
                                <p>Danh mục sản phẩm</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="productCategory">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="<?php echo e(route('admin.product-categories.index')); ?>">
                                            <span class="sub-item">Danh sách danh mục</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('admin.product-categories.create')); ?>">
                                            <span class="sub-item">Thêm mới danh mục</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#product">
                                <i class="fab fa-product-hunt"></i>
                                <p>Quản lý sản phẩm</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="product">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="<?php echo e(route('admin.products.index')); ?>">
                                            <span class="sub-item">Danh sách sản phẩm</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('admin.products.create')); ?>">
                                            <span class="sub-item">Thêm mới sản phẩm</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <a href="<?php echo e(route('admin.inventory.indexStock')); ?>">
                                <i class="fas fa-th-large"></i>
                                <p>Quản lý tồn kho</p>
                            </a>
                            <a href="<?php echo e(route('admin.commissions.index')); ?>">
                                <i class="fas fa-dollar-sign"></i>
                                <p>Cấu hình hoa hồng</p>
                            </a>
                        </li>
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Tin tức</h4>
                        </li>
                        
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#Category">
                                <i class="fas fa-folder"></i>
                                <p>Danh mục bài viết</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="Category">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="<?php echo e(route('admin.categories.index')); ?>">
                                            <span class="sub-item">Danh sách danh mục</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('admin.categories.create')); ?>">
                                            <span class="sub-item">Thêm mới danh mục</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#Aritcles">
                                <i class="fas fa-newspaper"></i>
                                <p>Quản lý bài viết</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="Aritcles">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="<?php echo e(route('admin.articles.index')); ?>">
                                            <span class="sub-item">Danh sách bài viết</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('admin.articles.create')); ?>">
                                            <span class="sub-item">Thêm mới bài viết</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Đơn hàng</h4>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.orders.index')); ?>">
                                <i class="fas fa-box"></i>
                                <p>Quản lý đơn hàng</p>
                                
                            </a>
                            
                        </li>
                        <li class="nav-section">
                            <span class="sidebar-mini-icon">
                                <i class="fa fa-ellipsis-h"></i>
                            </span>
                            <h4 class="text-section">Tài khoản</h4>
                        </li>
                        <li class="nav-item">
                            <a data-bs-toggle="collapse" href="#users">
                                <i class="fas fa-users-cog"></i>
                                <p>Quản lý tài khoản</p>
                                <span class="caret"></span>
                            </a>
                            <div class="collapse" id="users">
                                <ul class="nav nav-collapse">
                                    <li>
                                        <a href="<?php echo e(route('admin.users.index')); ?>">
                                            <span class="sub-item">Danh sách tài khoản</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <li class="nav-item">
                            <a href="/logout">
                                
                                <i class="fas fa-sign-out-alt"></i>
                                <p>Đăng xuất</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                        <a href="/admin" class="logo">
                            <img src="<?php echo e(asset('storage/' . $AppSetting->logo_path)); ?>" alt="navbar brand"
                                class="navbar-brand" height="60" style=" border-radius: 50%; " />
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar">
                                <i class="gg-menu-right"></i>
                            </button>
                            <button class="btn btn-toggle sidenav-toggler">
                                <i class="gg-menu-left"></i>
                            </button>
                        </div>
                        <button class="topbar-toggler more">
                            <i class="gg-more-vertical-alt"></i>
                        </button>
                    </div>
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav
                            class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="submit" class="btn btn-search pe-1">
                                        <i class="fa fa-search search-icon"></i>
                                    </button>
                                </div>
                                <input type="text" placeholder="Search ..." class="form-control" />
                            </div>
                        </nav>

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"
                                    role="button" aria-expanded="false" aria-haspopup="true">
                                    <i class="fa fa-search"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-search animated fadeIn">
                                    <form class="navbar-left navbar-form nav-search">
                                        <div class="input-group">
                                            <input type="text" placeholder="Search ..." class="form-control" />
                                        </div>
                                    </form>
                                </ul>
                            </li>
                            
                            <li class="nav-item topbar-icon dropdown hidden-caret">
                                <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-bell"></i>
                                    <span class="notification"><?php echo e($Countnotifications ?? 0); ?></span>
                                </a>
                                <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                                    <li>
                                        <div class="dropdown-title">
                                            Bạn có <?php echo e($Countnotifications ?? 0); ?> đơn hàng mới
                                        </div>
                                    </li>
                                    <li>
                                        <div class="notif-scroll scrollbar-outer">
                                            <div class="notif-center">
                                                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <a
                                                        href="<?php echo e(route('admin.orders.show', $notification->order_id)); ?>">
                                                        <div class="notif-icon notif-primary" style="min-width: 40px">
                                                            <i class="fas fa-cart-plus"></i>
                                                        </div>
                                                        <div class="notif-content">
                                                            <span class="block"> <?php echo e($notification->message); ?> </span>
                                                            <span
                                                                class="time"><?php echo e(\Carbon\Carbon::parse($notification->created_at)->diffInMinutes()); ?>

                                                                phút trước</span>
                                                        </div>
                                                    </a>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="see-all" href="<?php echo e(route('admin.orders.index')); ?>">Xem tất cả đơn
                                            hàng<i class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            

                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                                    aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="https://static.vecteezy.com/system/resources/previews/009/292/244/non_2x/default-avatar-icon-of-social-media-user-vector.jpg"
                                            alt="..." class="avatar-img rounded-circle" />
                                    </div>
                                    <span class="profile-username">
                                        <span class="op-7">Hi,</span>
                                        <span class="fw-bold"><?php echo e(Auth::user()->full_name); ?></span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <div class="dropdown-user-scroll scrollbar-outer">
                                        <li>
                                            <div class="user-box">
                                                <div class="avatar-lg">
                                                    <img src="https://static.vecteezy.com/system/resources/previews/009/292/244/non_2x/default-avatar-icon-of-social-media-user-vector.jpg"
                                                        alt="image profile" class="avatar-img rounded" />
                                                </div>
                                                <div class="u-text">
                                                    <h4><?php echo e(Auth::user()->full_name); ?></h4>
                                                    <p class="text-muted"><?php echo e(Auth::user()->email); ?></p>
                                                    <a href="profile.html"
                                                        class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            
                                            
                                            
                                            
                                            
                                            
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="<?php echo e(route('logout')); ?>">Logout</a>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>


                    <?php echo $__env->yieldContent('content'); ?>
                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between text-center">
                    
                    <div class="copyright w-100 text-center">
                        2025, Được thực hiện bởi
                        <a href="https://www.facebook.com/profile.php?id=100053830937586">Trần Huy Hưng</a> <i
                            class="fa fa-heart heart text-danger"></i>
                    </div>
                    
                </div>
            </footer>
        </div>

        <!-- Custom template | don't include it in your project! -->
        <div class="custom-template">
            <div class="title">Settings</div>
            <div class="custom-content">
                <div class="switcher">
                    <div class="switch-block">
                        <h4>Logo Header</h4>
                        <div class="btnSwitch">
                            <button type="button" class="selected changeLogoHeaderColor" data-color="dark"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="blue"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="purple"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="light-blue"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="green"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="orange"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="red"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="white"></button>
                            <br />
                            <button type="button" class="changeLogoHeaderColor" data-color="dark2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="blue2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="purple2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="light-blue2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="green2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="orange2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="red2"></button>
                        </div>
                    </div>
                    <div class="switch-block">
                        <h4>Navbar Header</h4>
                        <div class="btnSwitch">
                            <button type="button" class="changeTopBarColor" data-color="dark"></button>
                            <button type="button" class="changeTopBarColor" data-color="blue"></button>
                            <button type="button" class="changeTopBarColor" data-color="purple"></button>
                            <button type="button" class="changeTopBarColor" data-color="light-blue"></button>
                            <button type="button" class="changeTopBarColor" data-color="green"></button>
                            <button type="button" class="changeTopBarColor" data-color="orange"></button>
                            <button type="button" class="changeTopBarColor" data-color="red"></button>
                            <button type="button" class="selected changeTopBarColor" data-color="white"></button>
                            <br />
                            <button type="button" class="changeTopBarColor" data-color="dark2"></button>
                            <button type="button" class="changeTopBarColor" data-color="blue2"></button>
                            <button type="button" class="changeTopBarColor" data-color="purple2"></button>
                            <button type="button" class="changeTopBarColor" data-color="light-blue2"></button>
                            <button type="button" class="changeTopBarColor" data-color="green2"></button>
                            <button type="button" class="changeTopBarColor" data-color="orange2"></button>
                            <button type="button" class="changeTopBarColor" data-color="red2"></button>
                        </div>
                    </div>
                    <div class="switch-block">
                        <h4>Sidebar</h4>
                        <div class="btnSwitch">
                            <button type="button" class="changeSideBarColor" data-color="white"></button>
                            <button type="button" class="selected changeSideBarColor" data-color="dark"></button>
                            <button type="button" class="changeSideBarColor" data-color="dark2"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom-toggle">
                <i class="icon-settings"></i>
            </div>
        </div>
    </div>
    <script>
        window.AppSetting = {
            logoLight: "<?php echo e(asset('storage/' . $AppSetting->logo_path)); ?>",
            logoDark: "<?php echo e(asset('storage/' . $AppSetting->logo_path)); ?>"
        };
    </script>
    <!--   Core JS Files   -->
    <script src="/assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="/assets/js/core/popper.min.js"></script>
    <script src="/assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="/assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="/assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="/assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="/assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="/assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="/assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="/assets/js/kaiadmin.min.js"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="/assets/js/setting-demo.js"></script>
    <script src="/assets/js/demo.js"></script>
    
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <!-- Thêm Axios từ CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.querySelector("#logo_top").src = $ {
            $AppSetting - > image
        };
        document.querySelector(".logo-header").src = $ {
            $AppSetting - > image
        };


        Pusher.logToConsole = true;

        var pusher = new Pusher('ecc5ac907199f6950c31', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('orders');
        channel.bind('new.order.placed', function(data) {
            console.log(data);
            location.reload();

            $.notify({
                icon: 'icon-bell',
                title: 'Đơn hàng mới',
                message: 'Bạn có 1 đơn hàng mới từ' + data.order.name,
            }, {
                type: 'secondary',
                placement: {
                    from: "bottom",
                    align: "right"
                },
                time: 3000,
            });
            const data2 = {
                message: "Bạn có một đơn hàng mới từ " + data.order.name,
                order_id: data.order.id
            };

            axios.post('/admin/notifications', data2)
                .then(response => {
                    console.log('Thông báo đã được thêm:', response);
                    // Có thể reload bảng thông báo hoặc làm gì đó sau khi thêm thông báo
                    // loadNotifications(); // Hàm load lại thông báo

                })
                .catch(error => {
                    console.error('Lỗi khi thêm thông báo:', error);
                });


        });
        $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#177dff",
            fillColor: "rgba(23, 125, 255, 0.14)",
        });

        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#f3545d",
            fillColor: "rgba(243, 84, 93, .14)",
        });

        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#ffa534",
            fillColor: "rgba(255, 165, 52, .14)",
        });
    </script>
</body>

</html>
<?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/layout.blade.php ENDPATH**/ ?>