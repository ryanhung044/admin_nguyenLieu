

<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Yêu cầu rút tiền</h1>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Người yêu cầu</th>
                <th>Số tiền</th>
                <th>Trạng thái</th>
                <th>Chủ tài khoản</th>
                <th>Số tài khoản</th>
                <th>Ngân hàng</th>
                <th>Ảnh chứng từ</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $withdrawRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($request->id); ?></td>
                    <td><?php echo e($request->user->name ?? 'Không có'); ?></td>
                    <td><?php echo e(number_format($request->amount, 0, ',', '.')); ?> VND</td>
                    <td>
                        <?php if($request->status == 'pending'): ?>
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        <?php elseif($request->status == 'approved'): ?>
                            <span class="badge bg-success">Đã chuyển</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Từ chối</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($request->bankAccount->account_name ?? 'Chưa có'); ?></td>
                    <td><?php echo e($request->bankAccount->account_number ?? 'Chưa có'); ?></td>
                    <td><?php echo e($request->bankAccount->bank_name ?? 'Chưa có'); ?></td>
                    <td>
                        <?php if($request->image): ?>
                            <img src="<?php echo e(asset('storage/' . $request->image)); ?>" width="80" alt="Proof">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($request->created_at->format('d/m/Y H:i')); ?></td>
                    <td>
                        <?php if($request->status == 'pending'): ?>
                            <a href="<?php echo e(route('admin.bank-accounts.edit', $request->id)); ?>" class="btn btn-sm btn-primary">Cập
                                nhật</a>
                        <?php else: ?>
                        <?php endif; ?>
                        
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php echo e($withdrawRequests->links('pagination::bootstrap-5')); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/giaviet.store/resources/views/admin/account_payment/index.blade.php ENDPATH**/ ?>