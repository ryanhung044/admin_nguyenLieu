

<?php $__env->startSection('title', 'Lịch sử đơn hàng'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <h3 class="mb-4 mt-3">Lịch sử đơn hàng liên kết</h3>

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
                                $status = $statusLabels[$order->status] ?? [
                                    'label' => 'Không xác định',
                                    'class' => 'dark',
                                ];
                            ?>

                            <span class="badge bg-<?php echo e($status['class']); ?>  text-uppercase">
                                <?php echo e($status['label']); ?>

                            </span>

                        </div>
                    </div>
                    <div class="card-body">
                        <p><strong>Họ tên:</strong> <?php echo e($order->name); ?></p>
                        <p><strong>Ngày đặt:</strong> <?php echo e($order->created_at->format('d/m/Y H:i')); ?></p>
                        <p><strong>Tổng tiền:</strong> <?php echo e(number_format($order->total, 0, ',', '.')); ?>đ</p>

                        <ul class="list-group">
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo e(asset('storage/' . $item->thumbnail)); ?>" alt="<?php echo e($item->product_name); ?>"
                                            width="50" class="me-2">
                                        <span>
                                            <?php echo e($item->product_name); ?> (x<?php echo e($item->quantity); ?>)
                                            <span>
                                                Hoa hồng:<?php echo e(number_format($item->commission_amount, 0, ',', '.')); ?>đ
                                            </span>
                                        </span>
                                    </div>
                                    <span><?php echo e(number_format($item->price * $item->quantity, 0, ',', '.')); ?>đ</span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/ordersHistoryAffilate.blade.php ENDPATH**/ ?>