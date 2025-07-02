

<?php $__env->startSection('content'); ?>
    <h1 class="mb-4">Danh sách danh mục</h1>

    <a href="<?php echo e(route('admin.categories.create')); ?>" class="btn btn-primary mb-3">Thêm danh mục</a>

    <table id="categoryTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Slug</th>
                <th>Danh mục cha</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($cat->id); ?></td>
                    <td><?php echo e($cat->title); ?></td>
                    <td><?php echo e($cat->slug); ?></td>
                    <td><?php echo e($cat->parent->title ?? '-'); ?></td>
                    <td><?php echo e($cat->description); ?></td>
                    <td>
                        <a href="<?php echo e(route('admin.categories.edit', $cat->id)); ?>" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="<?php echo e(route('admin.categories.destroy', $cat->id)); ?>" method="POST"
                            style="display:inline-block">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Xóa danh mục này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/hace51f943/domains/lebaobinh.com/resources/views/admin/categories/index.blade.php ENDPATH**/ ?>