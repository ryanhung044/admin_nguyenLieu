<?php
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use App\Models\User;
    $AppSetting = \App\Models\appSetting::first();
    $banners = \App\Models\banner::where('position', 1)->where('status', 1)->get();
    $user = Auth::user();
    $products = \App\Models\Product::with('category', 'group')->latest()->take(10)->get();
    $topProducts = \App\Models\Product::select(
        'products.id',
        'products.name',
        'products.slug',
        'products.thumbnail',
        'products.sale_price',
        'products.stock',
        'products.price',
        DB::raw('SUM(order_items.quantity) as total_sold'),
    )
        ->join('order_items', 'products.id', '=', 'order_items.product_id')
        ->groupBy(
            'products.id',
            'products.name',
            'products.slug',
            'products.thumbnail',
            'products.sale_price',
            'products.stock',
            'products.price',
        )
        ->orderByDesc('total_sold')
        ->take(10)
        ->get();

    $articles = \App\Models\article::with('category')->latest()->take(10)->get();
    $menu1 = \App\Models\banner::where('position', 2)->where('status', 1)->get();
    // Controller
    $finalTop = DB::table('order_items')
        ->select('referrer_id', DB::raw('SUM(price * quantity) as total_sales'))
        ->whereNotNull('referrer_id')
        ->groupBy('referrer_id')
        ->orderByDesc('total_sales')
        ->take(20)
        ->get()
        ->map(function ($item) {
            $user = User::find($item->referrer_id);
            return [
                'id' => $item->referrer_id,
                'name' => $user?->full_name ?? 'Không rõ',
                'avatar_url' => $user?->avatar,
                'total_sales' => $item->total_sales,
            ];
        });

    $oldUser = session()->get('oldUser');
    if ($oldUser) {
        session()->put('oldUser', true);
    }

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($AppSetting->app_name); ?></title>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo e($AppSetting->app_name); ?>">
    <meta property="og:description" content="<?php echo e($AppSetting->description ?? $AppSetting->app_name); ?>">
    <meta property="og:image" content="<?php echo e(asset('storage/' . $AppSetting->logo_path)); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo e(asset('storage/' . $AppSetting->favicon_path)); ?>" type="image/x-icon" />
    <style>
        body {
            background-color: #f2f4f8;
            font-family: 'Arial', sans-serif;
            padding-bottom: 80px;
            margin: 0;
        }

        .header-fixed {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #fff;
            padding: 10px 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .banner img {
            width: 100%;
            border-radius: 10px;
        }

        .member-box,
        .wallet-box,
        .product-select-box {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            /* margin-top: 15px; */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .wallet-amount {
            font-size: 28px;
            font-weight: bold;
            color: #152379;
        }

        .btn-rounded {
            border-radius: 50px;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #152379;
            border-top: 1px solid #ddd;
            z-index: 999;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
        }

        .bottom-nav a {
            color: white;
            text-align: center;
            font-size: 13px;
            text-decoration: none;
        }

        .bottom-nav a .fa {
            display: block;
            font-size: 20px;
            margin-bottom: 3px;
        }

        .badge-noti {
            font-size: 10px;
            position: absolute;
            top: -3px;
            right: -6px;
        }

        .nav-icons {
            position: relative;
        }

        .backgound {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            background-color: #152379;
            border-bottom-left-radius: 70px;
            border-bottom-right-radius: 70px;
        }

        swiper-container {
            width: 100%;
        }

        swiper-slide {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 3px solid #ddd;
        }

        .name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .sales {
            color: #d97706;
            font-weight: bold;
        }

        .top-badge {
            position: absolute;
            top: 10px;
            left: 10px;
        }
    </style>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

</head>

<body>
    <div style="position: relative">
        <div class="backgound">
        </div>
        <div class="header" style="position: absolute;">
            <div class="logo-top d-flex align-items-center gap-3" style="position: absolute; top: 50px; left: 20px;">
                <img src="<?php echo e(asset('storage/' . $AppSetting->favicon_path)); ?>" alt=""
                    style="background: white; border-radius: 50%; height: 70px;">
                <span class="text-white fs-3 fw-bold text-uppercase text-nowrap">
                    <?php echo e($AppSetting->app_name ?? ''); ?>

                </span>
            </div>
        </div>
    </div>
    <div class="container" style="padding-top: 130px">
        <div class="wallet-box p-3 rounded-4 shadow-sm" style="position: relative">
            <div class="d-flex justify-content-between align-items-center">
                <div style="color: #152379;">
                    <p class="mb-1 fw-semibold">Tài khoản xác thực</p>
                    <p class="mb-0 fw-light small">Thành viên mới</p>
                </div>
                <div class="d-flex align-items-center" style="gap: 12px;">
                    <div style="color: #152379; text-align: right;">
                        <p class="mb-1 fw-light small">Xin chào,</p>
                        <p class="mb-0 fw-semibold text-uppercase"><?php echo e($user->full_name ?? ''); ?></p>
                    </div>
                    <div style="width: 48px;border-radius: 50%; overflow: hidden; border: 1px solid #ccc;">
                        <?php if($user): ?>
                            <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt="" class="img-fluid">
                        <?php else: ?>
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/User-avatar.svg/2048px-User-avatar.svg.png"
                                alt="avatar" class="img-fluid">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-decoration-none d-flex justify-content-between">
                    <a href="<?php echo e(route('account.agency')); ?>" class="text-decoration-none">
                        <div class="ml-3 d-flex flex-column align-items-center">
                            <div class="fw-bold fs-1" style="color: #152379">
                                <i class="fas fa-medal"></i>
                            </div>
                            <small class="text-muted">Tích điểm</small>
                        </div>
                    </a>

                    <a href="<?php echo e(route('orders.history')); ?>" class="text-decoration-none">

                        <div class="ml-3 d-flex flex-column align-items-center">
                            <div class="fw-bold fs-1" style="color: #152379"><i class="fas fa-cart-arrow-down"></i>
                            </div>
                            <small class="text-muted">Đơn hàng</small>
                        </div>
                    </a>

                    <a href="tel:0855862466" class="text-decoration-none">
                        <div class="ml-3 d-flex flex-column align-items-center">
                            <div class="fw-bold fs-1" style="color: #152379">
                                <i class="fas fa-comments"></i>
                            </div>
                            <small class="text-muted">Liên hệ</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="wallet-box p-3 rounded-4 shadow-sm mt-3">
            <div class="text-decoration-none d-flex justify-content-start gap-4">
                <?php $__currentLoopData = $menu1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e($menu->link); ?>" class="text-decoration-none">
                        <div class="ml-3 d-flex flex-column align-items-center">
                            <div style="width: 48px;border-radius: 50%; overflow: hidden; border: 1px solid #ccc;">
                                <?php if($menu): ?>
                                    <img src="<?php echo e(asset('storage/' . $menu->image)); ?>" alt="<?php echo e($menu->title); ?>"
                                        class="img-fluid">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('storage/' . $AppSetting->logo_path)); ?>" alt=""
                                        class="img-fluid">
                                <?php endif; ?>
                            </div>
                            <small class="text-muted mt-2 text-uppercase fw-bold"><?php echo e($menu->title); ?></small>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                
            </div>
        </div>

        <!-- Swiper Banner -->
        <div class="swiper banner-swiper mt-3">
            <div class="swiper-wrapper">
                <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="swiper-slide">
                        <a href="<?php echo e($banner->link); ?>">
                            <img src="<?php echo e(asset('storage/' . $banner->image)); ?>" alt="<?php echo e($banner->title); ?>"
                                class="img-fluid rounded-3 w-100">
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
            
            <div class="swiper-pagination"></div>
        </div>

        <div class="d-flex justify-content-between align-items-end mt-3">
            <span class="fw-bold fs-4" style="color: #152379">Sản phẩm bán chạy</span>
            <a href="<?php echo e(route('getAllProduct')); ?>" class="text-decoration-none" style="color: #ccc">Xem thêm</a>
        </div>
        <div class="swiper mySwiper mt-2 ">
            <div class="swiper-wrapper">
                <?php $__currentLoopData = $topProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="swiper-slide">
                        <div class="product-card p-2 rounded-3 shadow-sm bg-white">
                            <a href="<?php echo e(route('product.show', $product->slug)); ?>">
                                <img src="<?php echo e(asset('storage/' . $product->thumbnail)); ?>" class="w-100 rounded-2 mb-2"
                                    alt="">
                            </a>
                            <?php if($product->stock == 0): ?>
                                <div class="top-badge">
                                    <span class="badge bg-danger">Hết hàng</span>
                                </div>
                            <?php endif; ?>
                            <div class="small text-dark fw-medium"><?php echo e($product->name); ?></div>
                            <div>
                                <p class="text-danger">
                                    <?php if($product->price): ?>
                                        <span class="text-secondary text-decoration-line-through mb-1 "
                                            style="margin-right: 10px">
                                            <?php echo e(number_format($product->price, 0, ',', '.')); ?> VND
                                        </span>
                                    <?php endif; ?>
                                    <span class="fw-bold fs-5">
                                        <?php echo e(number_format($product->sale_price, 0, ',', '.')); ?> VND
                                    </span>
                                </p>
                                
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div class="small text-muted">
                                    <i class="fa fa-star text-warning"></i> 4.9 (<?php echo e($product->total_sold); ?> đã bán)
                                </div>
                                <div>
                                    <a href="<?php echo e(route('cart.add', $product->id)); ?>"
                                        class="btn btn-light rounded-circle border">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <button class="btn btn-light rounded-circle border share-product-btn"
                                        onclick="copyReferralLink('<?php echo e(route('product.show', $product->slug)); ?>?ref=<?php echo e(auth()->id()); ?>')">
                                        <i class="fa fa-share"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-end mt-4">
            <span class="fw-bold fs-4" style="color: #152379">Top thu nhập cao nhất</span>
        </div>
        <swiper-container class="topReferrersSwiper" init="false">
            <?php $__currentLoopData = $finalTop; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <swiper-slide>
                    <?php if($user['avatar_url']): ?>
                        <img src="<?php echo e(asset('storage/' . $user['avatar_url'])); ?>" alt="<?php echo e($user['name']); ?>"
                            class="avatar">
                    <?php else: ?>
                        <img src="https://static.vecteezy.com/system/resources/previews/009/292/244/non_2x/default-avatar-icon-of-social-media-user-vector.jpg"
                            alt="<?php echo e($user['name']); ?>" class="avatar">
                    <?php endif; ?>

                    <?php if($index == 0): ?>
                        <div class="top-badge">
                            <span class="badge bg-warning text-dark">TOP <?php echo e($index + 1); ?></span>
                        </div>
                    <?php elseif($index == 1): ?>
                        <div class="top-badge">
                            <span class="badge bg-secondary text-white">TOP <?php echo e($index + 1); ?></span>
                        </div>
                    <?php elseif($index == 2): ?>
                        <div class="top-badge">
                            <span class="badge bg-muted text-dark">TOP <?php echo e($index + 1); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="top-badge">
                            <span class="badge bg-light text-dark">TOP <?php echo e($index + 1); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="name text-center"><?php echo e($user['name']); ?></div>
                    <div class="sales text-center"><?php echo e(number_format($user['total_sales'], 0, ',', '.')); ?>₫</div>
                </swiper-slide>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </swiper-container>

        <div class="d-flex justify-content-between align-items-end mt-4">
            <span class="fw-bold fs-4" style="color: #152379">Tin tức</span>
            
        </div>
        <div class="swiper mySwiper mt-2 ">
            <div class="swiper-wrapper">
                <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="swiper-slide">
                        <div class="">
                            <a href="<?php echo e(route('article_detail', $article->slug)); ?>">
                                <img src="<?php echo e(asset('storage/' . $article->image)); ?>" class="w-100 rounded-4 mb-2"
                                    style="height: 300px; object-fit: cover;" alt="Article Image">
                            </a>
                            <h5 class="text-dark fw-bold"><?php echo e($article->title); ?></h5>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-end mt-5">
            <span class="fw-bold fs-4" style="color: #152379">Gợi ý cho bạn</span>
            <a href="<?php echo e(route('getAllProduct')); ?>" class="text-decoration-none" style="color: #ccc">Xem thêm</a>
        </div>
        <div class="swiper mySwiper mt-2 ">
            <div class="swiper-wrapper">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="swiper-slide">
                        <div class="product-card p-2 rounded-3 shadow-sm bg-white">
                            <a href="<?php echo e(route('product.show', $product->slug)); ?>">
                                <img src="<?php echo e(asset('storage/' . $product->thumbnail)); ?>" class="w-100 rounded-2 mb-2"
                                    alt="">
                            </a>
                            <?php if($product->stock == 0): ?>
                                <div class="top-badge">
                                    <span class="badge bg-danger">Hết hàng</span>
                                </div>
                            <?php endif; ?>
                            <div class="small text-dark fw-medium"><?php echo e($product->name); ?></div>
                            
                            <div>
                                <p class="text-danger">
                                    <?php if($product->price): ?>
                                        <span class="text-secondary text-decoration-line-through mb-1 "
                                            style="margin-right: 10px">
                                            <?php echo e(number_format($product->price, 0, ',', '.')); ?> VND
                                        </span>
                                    <?php endif; ?>
                                    <span class="fw-bold fs-5">
                                        <?php echo e(number_format($product->sale_price, 0, ',', '.')); ?> VND
                                    </span>
                                </p>
                                
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <div class="small text-muted">
                                    <i class="fa fa-star text-warning"></i> 4.9
                                </div>
                                <div>
                                    <a href="<?php echo e(route('cart.add', $product->id)); ?>"
                                        class="btn btn-light rounded-circle border">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <button id="shareProductBtn2" class="btn btn-light rounded-circle border"
                                        onclick="shareProduct('<?php echo e($product->name); ?>', '<?php echo e(route('product.show', $product->slug)); ?>?ref=<?php echo e(auth()->id()); ?>')">
                                        <i class="fa fa-share"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

    </div>
    <?php echo $__env->yieldContent('content'); ?>
    <?php if(session('success') || session('error')): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
            <div id="liveToast"
                class="toast align-items-center text-white <?php echo e(session('success') ? 'bg-success' : 'bg-danger'); ?> border-0 show"
                role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php echo e(session('success') ?? session('error')); ?>

                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="<?php echo e(route('home')); ?>">
            <i class="fa fa-home"></i>
            Trang chủ
        </a>
        <a href="<?php echo e(route('getAllProduct')); ?>">
            <i class="fa fa-box"></i>
            Sản phẩm
        </a>

        <a href="<?php echo e(route('cart.view')); ?>" class="position-relative nav-icons">
            <i class="fa fa-shopping-cart"></i>
            Giỏ hàng
            <span class="badge bg-danger badge-noti"><?php echo e(collect(session('cart', []))->sum('quantity')); ?></span>
        </a>

        <a href="/agency">
            <i class="fa fa-id-badge"></i>
            Đại lý
        </a>
        <a href="<?php echo e(route('account.index')); ?>">
            <i class="fa fa-user"></i>
            Tài khoản
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>

    <script>
        const swiperEl = document.querySelector('swiper-container');

        Object.assign(swiperEl, {
            loop: true,
            spaceBetween: 20,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                "@0.00": {
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                "@0.75": {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                "@1.00": {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
                "@1.50": {
                    slidesPerView: 4,
                    spaceBetween: 40,
                },
            },
        });

        swiperEl.initialize();
    </script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const toastEl = document.getElementById('liveToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });

        const swiper = new Swiper('.banner-swiper', {
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
        new Swiper(".mySwiper", {
            slidesPerView: 1,
            spaceBetween: 10,
            breakpoints: {
                576: {
                    slidesPerView: 2,
                    spaceBetween: 15,
                },
                768: {
                    slidesPerView: 3,
                    spaceBetween: 20,
                }
            },
            loop: false,
            autoplay: false,
        });

        function copyReferralLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                alert('Link giới thiệu đã được sao chép!');
            }).catch(err => {
                console.error('Không thể sao chép link', err);
            });
        }

        function shareProduct(name, url) {
            const shareData = {
                title: name,
                text: `Khám phá sản phẩm "${name}" này nhé!`,
                url: url
            };

            if (navigator.share) {
                navigator.share(shareData)
                    .then(() => console.log('✅ Chia sẻ thành công'))
                    .catch((err) => console.warn('❌ Chia sẻ bị huỷ hoặc lỗi:', err));
            } else {
                navigator.clipboard.writeText(url)
                    .then(() => alert('🔗 Link sản phẩm đã được sao chép!'))
                    .catch(() => alert('❌ Không thể sao chép link'));
            }
        }
    </script>
</body>

</html>
<?php /**PATH C:\Users\DIEN MAY XANH\Desktop\Laravel\admin_lebaobinh\resources\views/layout2.blade.php ENDPATH**/ ?>