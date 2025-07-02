<?php $__env->startSection('content'); ?>
<h1>Quản lý cấu hình hoa hồng</h1>
<a href="<?php echo e(route('admin.commissions.create')); ?>" class="btn btn-primary mb-3">Thêm cấu hình</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Danh mục</th>
            <th>Cấp độ</th>
            <th>Phần trăm (%)</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $commissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($item->category->name ?? 'Không rõ'); ?></td>
                <td>F<?php echo e($item->level); ?></td>
                <td><?php echo e($item->percentage); ?></td>
                <td>
                    <a href="<?php echo e(route('admin.commissions.edit', $item->id)); ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="<?php echo e(route('admin.commissions.destroy', $item->id)); ?>" method="POST" style="display:inline-block">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button onclick="return confirm('Xóa cấu hình này?')" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DIEN MAY XANH\Desktop\Laravel\admin_nguyenLieu\resources\views/admin/commissions/index.blade.php ENDPATH**/ ?>