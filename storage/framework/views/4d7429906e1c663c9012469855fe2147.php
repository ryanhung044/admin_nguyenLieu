

<?php $__env->startSection('title', $product->name); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $images = json_decode($product->images, true) ?? [];
    ?>
    <style>
        .bottom-nav {
            display: none;
        }

        .bottom-nav2 {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 55%;
            background: white;
            border-top: 2px solid #ccc;
            z-index: 999;
            display: flex;
            justify-content: space-around;
            padding: 14px 0;
        }

        .bottom-nav3 {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 45%;
            background: #152379;
            border-top: 2px solid #ccc;
            z-index: 999;
            display: flex;
            justify-content: space-around;
            padding: 14px 0;
        }

        .bottom-nav2 a {
            color: #152379;
            text-align: center;
            font-size: 13px;
            text-decoration: none;
        }

        .bottom-nav3 a {
            color: white;
            text-align: center;
            font-size: 13px;
            text-decoration: none;
        }

        .bottom-nav2 a .fa,
        .bottom-nav3 a .fa {
            display: block;
            font-size: 20px;
            margin-bottom: 3px;
        }
    </style>
    <div>
        <div class="swiper productSwiper mb-4" >
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="<?php echo e(asset('storage/' . $product->thumbnail)); ?>" alt="Thumbnail">
                </div>

                <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="swiper-slide">
                        <img src="<?php echo e(asset('storage/' . str_replace('\\', '', $img))); ?>" alt="Ảnh sản phẩm">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>

            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
        <div class="container py-4" style="background: white">
            <div class="product-info">
                <h4 class="mb-2"><?php echo e($product->name); ?></h4>
                <p class="text-danger price mb-2">
                    <?php if($product->price): ?>
                        <span class="text-secondary text-decoration-line-through mb-1 " style="margin-right: 10px;font-size: .8em">
                            <?php echo e(number_format($product->price, 0, ',', '.')); ?> VND
                        </span>
                    <?php endif; ?>
                    <span class="fw-bold fs-5">
                        <?php echo e(number_format($product->sale_price, 0, ',', '.')); ?> VND
                    </span>
                </p>
                
                <div class="text-muted small mb-4">Tồn kho: <?php echo e($product->stock); ?></div>

            </div>
            <div class="product-info">
                
                <div class="d-flex justify-content-between align-items-center btn btn-primary w-100 mt-3 px-5">
                    <span class="fw-bold">
                        <?php if($product->sale_price * ($product->commission_rate / 100) != 0): ?>
                            Chia sẻ để nhận ngay
                            <?php echo e(number_format($product->sale_price * ($product->commission_rate / 100), 0, ',', '.')); ?>đ
                        <?php else: ?>
                            Chia sẻ để nhận ngay 400K
                        <?php endif; ?>
                    </span>
                    <i class="fas fa-share" style="font-size: 2em"></i>
                </div>
            </div>
            <?php if($product->content): ?>
                <div class="mt-4">
                    <h5 class="fw-bold mb-2">Mô tả sản phẩm</h5>
                    <div class="text-muted" style="line-height: 1.6">
                        <?php echo $product->content; ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="bottom-nav2">
            <a href="<?php echo e(route('cart.add', $product->id)); ?>" class="d-flex align-items-center gap-2">
                <i class="fa fa-cart-plus me-1"></i>
                <span class="fw-bold fs-5">Thêm vào giỏ hàng</span>
            </a>


        </div>
        <div class="bottom-nav3">
            <a href="<?php echo e(route('cart.view')); ?>" class="position-relative nav-icons d-flex align-items-center gap-2">
                <i class="fa fa-shopping-cart"></i>
                <span class="fw-bold fs-5">Xem giỏ hàng</span>
                
            </a>


        </div>
    </div>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper(".productSwiper", {
            loop: true,
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });
    </script>

    <style>
        .swiper-slide img {
            /* height: 300px; */
            object-fit: cover;
            border-radius: 1rem;
            width: 100%;
        }

        .product-info h4 {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .price {
            font-size: 1.25rem;
        }

        .swiper {
            border-radius: 1rem;
            overflow: hidden;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/product_detail.blade.php ENDPATH**/ ?>