<?php $__env->startSection('title', 'Trang đại lý'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container mt-4 px-3">
        <h5 class="fw-bold mb-3">Giỏ hàng của bạn</h5>
        <?php if(empty($carts) || count($carts) === 0): ?>
            <div class="text-center py-5">
                <h5 class="mb-3">Giỏ hàng của bạn đang trống</h5>
                <a href="<?php echo e(route('getAllProduct')); ?>" class="btn btn-primary">Mua sắm ngay</a>
            </div>
        <?php else: ?>
            <?php $__currentLoopData = $carts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="cart-item d-flex gap-3 mb-3 bg-white p-3 rounded-3 shadow-sm">
                    <img src="<?php echo e(asset('storage/' . $details['image'])); ?>" class="rounded-2" alt="SP"
                        style="width: 80px; height: 80px; object-fit: cover;">

                    <div class="flex-grow-1">
                        <div class="fw-semibold"><?php echo e($details['name']); ?></div>
                        <div class="text-danger fw-bold"><?php echo e(number_format($details['price'], 0, ',', '.')); ?>đ</div>
                        <div class="d-flex align-items-center mt-2">
                            <a href="<?php echo e(route('cart.decrease', $id)); ?>" class="btn btn-outline-secondary btn-sm">-</a>
                            <input type="text" class="form-control form-control-sm mx-2 text-center" style="width: 50px;"
                                value="<?php echo e($details['quantity']); ?>" readonly>
                            <a href="<?php echo e(route('cart.increase', $id)); ?>" class="btn btn-outline-secondary btn-sm">+</a>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="<?php echo e(route('cart.remove', $id)); ?>" class="btn btn-link btn-danger"><i
                                class="fa fa-trash text-danger"></i></a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <form action="<?php echo e(route('order.place')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
                    <h6 class="fw-bold mb-2">Thông tin nhận hàng</h6>
                    <div class="mb-2">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập họ và tên" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Địa chỉ nhận hàng</label>
                        <input type="text" name="address" class="form-control"
                            placeholder="Số nhà, đường, quận, thành phố" required>
                    </div>
                </div>

                <div class="bg-white p-3 rounded-3 shadow-sm mb-3">
                    <h6 class="fw-bold mb-2">Phương thức thanh toán</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD"
                            checked>
                        <label class="form-check-label" for="cod">Thanh toán khi nhận hàng (COD)</label>
                    </div> 
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="VNPAY" >
                        <label class="form-check-label" for="vnpay">Chuyển khoản ngân hàng</label>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 mb-3">
                    <div class="fw-bold">Tổng cộng</div>
                    <div class="fw-bold text-danger"><?php echo e(number_format($total, 0, ',', '.')); ?>đ</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-semibold">
                    Thanh toán
                </button>
            </form>

        <?php endif; ?>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DIEN MAY XANH\Desktop\Laravel\admin_lebaobinh\resources\views/cart.blade.php ENDPATH**/ ?>