

<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Danh sách tài khoản</h1>

    <!-- Thêm sản phẩm -->
    <a href="<?php echo e(route('admin.banners.create')); ?>" class="btn btn-primary mb-3">Thêm banner</a>

    <!-- Bảng sản phẩm -->
    <table id="bannerTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Hình ảnh</th>
                <th>Tiêu đề</th>
                <th>Link</th>
                <th>Vị trí</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $banners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $banner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($banner->id); ?></td>
                    <td>
                        <?php if($banner->image): ?>
                            <img src="<?php echo e(asset('storage/' . $banner->image)); ?>" width="80" alt="Banner">
                        <?php else: ?>
                            <span class="text-muted">Không có ảnh</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($banner->title); ?></td>
                    <td><a href="<?php echo e($banner->link); ?>" target="_blank"><?php echo e($banner->link); ?></a></td>
                    <td><?php echo e($banner->position); ?></td>
                    <td><?php echo e($banner->start_date ? \Carbon\Carbon::parse($banner->start_date)->format('d/m/Y H:i') : '-'); ?>

                    </td>
                    <td><?php echo e($banner->end_date ? \Carbon\Carbon::parse($banner->end_date)->format('d/m/Y H:i') : '-'); ?></td>
                    <td>
                        <form action="<?php echo e(route('admin.banners.toggleStatus', $banner->id)); ?>" method="POST"
                            style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="badge fs-5 <?php echo e($banner->status ? 'bg-success' : 'bg-secondary'); ?>">
                                <?php echo e($banner->status ? 'Kích hoạt' : 'Tắt'); ?>

                            </button>
                        </form>

                    </td>
                    <td>
                        <a href="<?php echo e(route('admin.banners.edit', $banner->id)); ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="<?php echo e(route('admin.banners.destroy', $banner->id)); ?>" method="POST"
                            style="display:inline-block">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc muốn xóa banner này không?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/lebaobinh.com/resources/views/admin/banners/index.blade.php ENDPATH**/ ?>