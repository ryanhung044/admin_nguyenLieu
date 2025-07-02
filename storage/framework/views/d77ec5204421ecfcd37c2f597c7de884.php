<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h3 class="text-center mb-4">CHI TIẾT ĐƠN HÀNG #<?php echo e($order->id); ?></h3>

    <div class="row">
        <!-- Box 1: Thông tin người nhận -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header  fw-bold">
                    Thông tin người nhận
                </div>
                <div class="card-body">
                    <p><strong>Họ tên:</strong> <?php echo e($order->name); ?></p>
                    <p><strong>Số điện thoại:</strong> <?php echo e($order->phone); ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo e($order->address); ?></p>
                    <p><strong>Thời gian đặt:</strong> <?php echo e($order->created_at->format('H:i d/m/Y')); ?></p>
                    <p><strong>Phương thức thanh toán:</strong> <?php echo e($order->payment_method); ?></p>
                    <p><strong>Trạng thái thanh toán:</strong> 
                        <span class="badge bg-<?php echo e($order->payment_status == 'paid' ? 'success' : 'danger'); ?>">
                            <?php echo e($order->payment_status == 'paid' ? 'ĐÃ THANH TOÁN' : 'CHƯA THANH TOÁN'); ?>

                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Box 2: Thông tin đơn hàng -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-bold">
                    Thông tin đơn hàng
                </div>
                <div class="card-body">
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p>
                            <img src="<?php echo e(asset('storage/' . $item->product->thumbnail)); ?>" alt="<?php echo e($item->product->name); ?>" style="width: 50px; height: 50px; object-fit: cover;" class="me-3">
                            
                            <strong><?php echo e($item->product->name); ?></strong><br>
                            Giá: <?php echo e(number_format($item->price, 0, ',', '.')); ?>đ |
                            SL: <?php echo e($item->quantity); ?> |
                            Thành tiền: <?php echo e(number_format($item->price * $item->quantity, 0, ',', '.')); ?>đ
                        </p>
                        <hr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <p><strong>Tạm tính:</strong> <?php echo e(number_format($order->subtotal, 0, ',', '.')); ?>đ</p>
                    <p><strong>Phí vận chuyển:</strong> <?php echo e(number_format($order->shipping_fee, 0, ',', '.')); ?>đ</p>
                    <p class="fw-bold fs-5"><strong>Tổng tiền:</strong> <?php echo e(number_format($order->total, 0, ',', '.')); ?>đ</p>
                    
                    <p><strong>Trạng thái đơn hàng:</strong>
                        <?php
                            $statusLabels = [
                                'pending' => ['label' => 'Khởi tạo', 'class' => 'secondary'],
                                'approved' => ['label' => 'Duyệt', 'class' => 'info'],
                                'packed' => ['label' => 'Đóng gói', 'class' => 'primary'],
                                'shipped' => ['label' => 'Xuất kho', 'class' => 'warning'],
                                'completed' => ['label' => 'Hoàn thành', 'class' => 'success'],
                                'cancelled' => ['label' => 'Hủy đơn', 'class' => 'danger'],
                            ];
                            $status = $statusLabels[$order->status] ?? ['label' => 'Không xác định', 'class' => 'dark'];
                        ?>
                        <span class="badge bg-<?php echo e($status['class']); ?>"><?php echo e($status['label']); ?></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end">
        
        <a href="<?php echo e(route('admin.orders.invoice', $order->id)); ?>" target="_blank" class="btn btn-secondary mt-3">
            <i class="fas fa-print"></i> In hóa đơn
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DIEN MAY XANH\Desktop\Laravel\admin_lebaobinh\resources\views/admin/orders/edit.blade.php ENDPATH**/ ?>