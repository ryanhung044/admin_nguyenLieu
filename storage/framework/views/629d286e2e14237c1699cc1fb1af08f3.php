

<?php $__env->startSection('title', 'Lịch sử đơn hàng'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <h3 class="mb-4 mt-3">Lịch sử đơn hàng</h3>

        <?php if($orders->isEmpty()): ?>
            <p>Bạn chưa có đơn hàng nào.</p>
        <?php else: ?>
            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Mã đơn: #<?php echo e($order->id); ?></strong>
                        <div>
                            <?php
                                $statusLabels = [
                                    'pending' => ['label' => 'Khởi tạo', 'class' => 'secondary'],
                                    'approved' => ['label' => 'Duyệt', 'class' => 'info'],
                                    'packed' => ['label' => 'Đóng gói', 'class' => 'primary'],
                                    'shipped' => ['label' => 'Xuất kho', 'class' => 'warning'],
                                    'completed' => ['label' => 'Hoàn thành', 'class' => 'success'],
                                    'cancelled' => ['label' => 'Hủy đơn', 'class' => 'danger'],
                                ];
                                $statusPaymentLabels = [
                                    'pending' => ['label' => 'Chờ thanh toán', 'class' => 'danger'],
                                    'paid' => ['label' => 'Đã thanh toán', 'class' => 'success'],
                                    'failed' => ['label' => 'Thất bại', 'class' => 'danger'],
                                    'refunded' => ['label' => 'Hoàn tiền', 'class' => 'primary'],
                                ];
                                $status = $statusLabels[$order->status] ?? [
                                    'label' => 'Không xác định',
                                    'class' => 'dark',
                                ];
                                $paymentMethodLabels = [
                                    'COD' => 'Thanh toán khi nhận hàng',
                                    'VNPAY' => 'Chuyển khoản ngân hàng',
                                ];

                            ?>
                            <?php if(!in_array($order->status, ['completed', 'cancelled'])): ?>
                                <div class="dropdown d-inline ms-2">
                                    <a class="btn btn-sm btn-light dropdown-toggle" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <?php if($order->payment_method == 'VNPAY' && ($order->status_payment != 'paid' && $order->status_payment != 'refunded')): ?>
                                            <li>
                                                <form action="<?php echo e(route('vnpay.checkout', $order->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    
                                                    <input type="hidden" name="id" value="<?php echo e($order->id); ?>">
                                                    <input type="hidden" name="total" value="<?php echo e($order->total); ?>">
                                                    <button type="submit" class="dropdown-item text-primary">
                                                        <i class="bi bi-x-circle me-1"></i> Thanh toán
                                                    </button>
                                                </form>
                                            </li>
                                        <?php endif; ?>
                                        <li>
                                            <form action="<?php echo e(route('orders.cancel', $order->id)); ?>" method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="bi bi-x-circle me-1"></i> Hủy đơn
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <span class="badge bg-<?php echo e($status['class']); ?>  text-uppercase">
                                <?php echo e($status['label']); ?>

                            </span>

                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Ngày đặt:</strong> <?php echo e($order->created_at->format('d/m/Y H:i')); ?></p>
                        <p><strong>Tổng tiền:</strong> <?php echo e(number_format($order->total, 0, ',', '.')); ?> VND</p>
                        <p><strong>Phương thức thanh toán:</strong> <?php echo e($paymentMethodLabels[$order->payment_method] ?? $order->payment_method); ?></p>
                        <p>
                            <strong>Trạng thái thanh toán:</strong>
                            <?php
                                $paymentStatus = $statusPaymentLabels[$order->status_payment] ?? ['label' => 'Không xác định', 'class' => 'dark'];
                            ?>
                            <span class="badge bg-<?php echo e($paymentStatus['class']); ?>"><?php echo e($paymentStatus['label']); ?></span>
                        </p>

                        <ul class="list-group">
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex">
                                        <img src="<?php echo e(asset('storage/' . $item->thumbnail)); ?>"
                                            alt="<?php echo e($item->product_name); ?>" width="50" class="me-2">
                                        <?php echo e($item->product_name); ?> (x<?php echo e($item->quantity); ?>)
                                    </div>
                                    <span><?php echo e(number_format($item->price * $item->quantity, 0, ',', '.')); ?> VND</span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/ordersHistory.blade.php ENDPATH**/ ?>