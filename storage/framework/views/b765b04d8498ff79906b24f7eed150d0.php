<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Danh sách đơn hàng</h1>
    
    <div class="d-flex justify-content-between align-items-center mb-3" style="overflow: auto">
        <div class="btn-group" role="group" aria-label="Lọc theo trạng thái">
            <?php
                $statusFilters = [
                    'all' => 'Tất cả',
                    'pending' => 'Khởi tạo',
                    'approved' => 'Duyệt',
                    'packed' => 'Đóng gói',
                    'shipped' => 'Xuất kho',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Hủy đơn',
                ];
            ?>
            <?php $__currentLoopData = $statusFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.orders.index', ['status' => $key])); ?>"
                    class="btn <?php echo e(request('status', 'all') == $key ? 'btn-primary' : 'btn-outline-primary'); ?> me-2">
                    <?php echo e($label); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="d-flex justify-content-between align-items-center gap-1">
            <form method="GET" action="<?php echo e(route('admin.orders.index')); ?>"
                class="d-flex justify-content-between align-items-center ">
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Tìm kiếm đơn hàng..." class="form-control d-inline-block" style="min-width: 150px">
                <button type="submit" class="btn btn-primary">Tìm</button>
            </form>

            <div>
                <a href="<?php echo e(route('admin.orders.export')); ?>" class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Xuất Excel
                </a>
            </div>

        </div>
    </div>


    <div style="overflow-x: auto;">
        <table id="productTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Số điện thoại</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Trạng thái thanh toán</th>
                    <th>Mặt hàng</th>
                    <th>Người giới thiệu</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($order->id); ?></td>
                        <td><?php echo e($order->name); ?></td>
                        <td><?php echo e($order->phone); ?></td>
                        <td><?php echo e(number_format($order->total, 0, ',', '.')); ?> VND</td>
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
                            $statusPaymentLabels = [
                                'pending' => ['label' => 'Chờ thanh toán', 'class' => 'danger'],
                                'paid' => ['label' => 'Đã thanh toán', 'class' => 'success'],
                                'failed' => ['label' => 'Thất bại', 'class' => 'danger'],
                                'refunded' => ['label' => 'Hoàn tiền', 'class' => 'primary'],
                            ];
                            $statusPayment = $statusPaymentLabels[$order->status_payment];
                        ?>

                        <td>
                            <span class="badge fs-5 bg-<?php echo e($status['class']); ?>">
                                <?php echo e($status['label']); ?>

                            </span>
                        </td>

                        <td>
                            <span class="badge fs-5 bg-<?php echo e($statusPayment['class']); ?>">
                                <?php echo e($statusPayment['label']); ?>

                            </span>
                        </td>

                        <td>
                            <?php if($order->items): ?>
                                <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($item->product): ?>
                                        <div>- <?php echo e($item->product_name); ?> (x<?php echo e($item->quantity); ?>)</div>
                                    <?php else: ?>
                                        <div>- Sản phẩm không tồn tại (x<?php echo e($item->quantity); ?>)</div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if($order->referrer): ?>
                                <span class="badge fs-5 bg-secondary ">
                                    [#<?php echo e($order->referrer->id); ?>] - <?php echo e($order->referrer->full_name); ?>

                                </span>
                            <?php else: ?>
                                <span class="badge fs-5 bg-danger">Không có</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="<?php echo e(route('admin.orders.show', $order->id)); ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button"
                                        id="dropdownMenuButton<?php echo e($order->id); ?>" data-bs-toggle="dropdown"
                                        aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo e($order->id); ?>">
                                        <!-- Mở popup cập nhật trạng thái -->
                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#updateStatusModal" data-id="<?php echo e($order->id); ?>"
                                                data-status="<?php echo e($order->status); ?>">
                                                Cập nhật trạng thái
                                            </a>
                                        </li>

                                        <li>
                                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#updateStatusPaymentModal" data-id="<?php echo e($order->id); ?>"
                                                data-statusPayment="<?php echo e($order->status_payment); ?>">
                                                Trạng thái thanh toán
                                            </a>
                                        </li>

                                        <!-- Hủy đơn -->
                                        <li>
                                            <form action="<?php echo e(route('admin.orders.updateStatus', $order->id)); ?>"
                                                method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                                                    Hủy đơn
                                                </button>
                                            </form>
                                        </li>

                                    </ul>
                                </div>
                                <a href="<?php echo e(route('admin.orders.invoice', $order->id)); ?>" target="_blank"
                                    class="btn btn-secondary btn-sm">
                                    <i class="fas fa-print"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </tbody>


        </table>
    </div>
    <?php echo e($orders->links('pagination::bootstrap-5')); ?>

    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="updateStatusForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cập nhật trạng thái đơn hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="statusSelect" class="form-label">Trạng thái</label>
                            <select class="form-select" id="statusSelect" name="status">
                                <option value="pending">Khởi tạo</option>
                                <option value="approved">Duyệt</option>
                                <option value="packed">Đóng gói</option>
                                <option value="shipped">Xuất kho</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="cancelled">Hủy đơn</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Cập nhật</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="updateStatusPaymentModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form id="updateStatusPaymentForm" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-content">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cập nhật trạng thái thanh toán</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="statusSelect" class="form-label">Trạng thái</label>
                                <select class="form-select" id="statusSelect" name="status_payment">
                                    <option value="pending">Chờ thanh toán</option>
                                    <option value="paid">Đã thanh toán</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Cập nhật</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        // console.log('window.Echo' . window.Echo); 
        document.addEventListener('DOMContentLoaded', function() {
            const updateStatusModal = document.getElementById('updateStatusModal');
            const updateStatusPaymentModal = document.getElementById('updateStatusPaymentModal');
            updateStatusModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-status');

                // Gán form action
                const form = document.getElementById('updateStatusForm');
                form.action = `/admin/orders/${orderId}/status`;

                // Gán trạng thái hiện tại
                document.getElementById('statusSelect').value = currentStatus;
            });
            updateStatusPaymentModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-id');
                const currentStatus = button.getAttribute('data-statusPayment');

                // Gán form action
                const form = document.getElementById('updateStatusPaymentForm');
                form.action = `/admin/orders/${orderId}/updateStatusPayment`;

                // Gán trạng thái hiện tại
                document.getElementById('statusSelect').value = currentStatus;
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DIEN MAY XANH\Desktop\Laravel\admin_lebaobinh\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>